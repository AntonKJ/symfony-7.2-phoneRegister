<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authorization;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Strategy\AccessDecisionStrategyInterface;
use Symfony\Component\Security\Core\Authorization\Strategy\AffirmativeStrategy;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\TraceableVoter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

/**
 * AccessDecisionManager is the base class for all access decision managers
 * that use decision voters.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class AccessDecisionManager implements AccessDecisionManagerInterface
{
    private const VALID_VOTES = [
        VoterInterface::ACCESS_GRANTED => true,
        VoterInterface::ACCESS_DENIED => true,
        VoterInterface::ACCESS_ABSTAIN => true,
    ];

    private array $votersCacheAttributes = [];
    private array $votersCacheObject = [];
    private AccessDecisionStrategyInterface $strategy;
    private array $accessDecisionStack = [];

    /**
     * @param iterable<mixed, VoterInterface> $voters An array or an iterator of VoterInterface instances
     */
    public function __construct(
        private iterable $voters = [],
        ?AccessDecisionStrategyInterface $strategy = null,
    ) {
        $this->strategy = $strategy ?? new AffirmativeStrategy();
    }

    /**
     * @param bool $allowMultipleAttributes Whether to allow passing multiple values to the $attributes array
     */
    public function decide(TokenInterface $token, array $attributes, mixed $object = null, bool|AccessDecision|null $accessDecision = null, bool $allowMultipleAttributes = false): bool
    {
        if (\is_bool($accessDecision)) {
            $allowMultipleAttributes = $accessDecision;
            $accessDecision = null;
        }

        // Special case for AccessListener, do not remove the right side of the condition before 6.0
        if (\count($attributes) > 1 && !$allowMultipleAttributes) {
            throw new InvalidArgumentException(\sprintf('Passing more than one Security attribute to "%s()" is not supported.', __METHOD__));
        }

        $accessDecision ??= end($this->accessDecisionStack) ?: new AccessDecision();
        $this->accessDecisionStack[] = $accessDecision;

        $accessDecision->strategy = $this->strategy instanceof \Stringable ? $this->strategy : get_debug_type($this->strategy);

        try {
            return $accessDecision->isGranted = $this->strategy->decide(
                $this->collectResults($token, $attributes, $object, $accessDecision)
            );
        } finally {
            array_pop($this->accessDecisionStack);
        }
    }

    /**
     * @return \Traversable<int, VoterInterface::ACCESS_*>
     */
    private function collectResults(TokenInterface $token, array $attributes, mixed $object, AccessDecision $accessDecision): \Traversable
    {
        foreach ($this->getVoters($attributes, $object) as $voter) {
            $vote = new Vote();
            $result = $voter->vote($token, $object, $attributes, $vote);

            if (!\is_int($result) || !(self::VALID_VOTES[$result] ?? false)) {
                throw new \LogicException(\sprintf('"%s::vote()" must return one of "%s" constants ("ACCESS_GRANTED", "ACCESS_DENIED" or "ACCESS_ABSTAIN"), "%s" returned.', get_debug_type($voter), VoterInterface::class, var_export($result, true)));
            }

            $voter = $voter instanceof TraceableVoter ? $voter->getDecoratedVoter() : $voter;
            $vote->voter = $voter instanceof \Stringable ? $voter : get_debug_type($voter);
            $vote->result = $result;
            $accessDecision->votes[] = $vote;

            yield $result;
        }
    }

    /**
     * @return iterable<int, VoterInterface>
     */
    private function getVoters(array $attributes, $object = null): iterable
    {
        $keyAttributes = [];
        foreach ($attributes as $attribute) {
            $keyAttributes[] = \is_string($attribute) ? $attribute : null;
        }
        // use `get_class` to handle anonymous classes
        $keyObject = \is_object($object) ? $object::class : get_debug_type($object);
        foreach ($this->voters as $key => $voter) {
            if (!$voter instanceof CacheableVoterInterface) {
                yield $voter;
                continue;
            }

            $supports = true;
            // The voter supports the attributes if it supports at least one attribute of the list
            foreach ($keyAttributes as $keyAttribute) {
                if (null === $keyAttribute) {
                    $supports = true;
                } elseif (!isset($this->votersCacheAttributes[$keyAttribute][$key])) {
                    $this->votersCacheAttributes[$keyAttribute][$key] = $supports = $voter->supportsAttribute($keyAttribute);
                } else {
                    $supports = $this->votersCacheAttributes[$keyAttribute][$key];
                }
                if ($supports) {
                    break;
                }
            }
            if (!$supports) {
                continue;
            }

            if (!isset($this->votersCacheObject[$keyObject][$key])) {
                $this->votersCacheObject[$keyObject][$key] = $supports = $voter->supportsType($keyObject);
            } else {
                $supports = $this->votersCacheObject[$keyObject][$key];
            }
            if (!$supports) {
                continue;
            }
            yield $voter;
        }
    }
}
