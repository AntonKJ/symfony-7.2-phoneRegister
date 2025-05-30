<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Tests\Console\Descriptor;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Csrf\Type\FormTypeCsrfExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormType;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

abstract class AbstractDescriptorTestCase extends TestCase
{
    private string|false $colSize;

    protected function setUp(): void
    {
        $this->colSize = getenv('COLUMNS');
        putenv('COLUMNS='.(119 + \strlen(\PHP_EOL)));
    }

    protected function tearDown(): void
    {
        putenv($this->colSize ? 'COLUMNS='.$this->colSize : 'COLUMNS');
    }

    /** @dataProvider getDescribeDefaultsTestData */
    public function testDescribeDefaults($object, array $options, $fixtureName)
    {
        $describedObject = $this->getObjectDescription($object, $options);
        $expectedDescription = $this->getExpectedDescription($fixtureName);

        if ('json' === $this->getFormat()) {
            $this->assertEquals(json_encode(json_decode($expectedDescription), \JSON_PRETTY_PRINT), json_encode(json_decode($describedObject), \JSON_PRETTY_PRINT));
        } else {
            $this->assertEquals(trim($expectedDescription), trim(str_replace(\PHP_EOL, "\n", $describedObject)));
        }
    }

    /** @dataProvider getDescribeResolvedFormTypeTestData */
    public function testDescribeResolvedFormType(ResolvedFormTypeInterface $type, array $options, $fixtureName)
    {
        $describedObject = $this->getObjectDescription($type, $options);
        $expectedDescription = $this->getExpectedDescription($fixtureName);

        if ('json' === $this->getFormat()) {
            $this->assertEquals(json_encode(json_decode($expectedDescription), \JSON_PRETTY_PRINT), json_encode(json_decode($describedObject), \JSON_PRETTY_PRINT));
        } else {
            $this->assertEquals(trim($expectedDescription), trim(str_replace(\PHP_EOL, "\n", $describedObject)));
        }
    }

    /** @dataProvider getDescribeOptionTestData */
    public function testDescribeOption(OptionsResolver $optionsResolver, array $options, $fixtureName)
    {
        $describedObject = $this->getObjectDescription($optionsResolver, $options);
        $expectedDescription = $this->getExpectedDescription($fixtureName);

        if ('json' === $this->getFormat()) {
            $this->assertEquals(json_encode(json_decode($expectedDescription), \JSON_PRETTY_PRINT), json_encode(json_decode($describedObject), \JSON_PRETTY_PRINT));
        } else {
            $this->assertStringMatchesFormat(trim($expectedDescription), trim(str_replace(\PHP_EOL, "\n", $describedObject)));
        }
    }

    public static function getDescribeDefaultsTestData()
    {
        $options['core_types'] = ['Symfony\Component\Form\Extension\Core\Type\FormType'];
        $options['service_types'] = ['Symfony\Bridge\Doctrine\Form\Type\EntityType'];
        $options['extensions'] = ['Symfony\Component\Form\Extension\Csrf\Type\FormTypeCsrfExtension'];
        $options['guessers'] = ['Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser'];
        $options['decorated'] = false;
        $options['show_deprecated'] = false;
        yield [null, $options, 'defaults_1'];

        $options['core_types'] = [];
        $options['service_types'] = [FooType::class];
        $options['show_deprecated'] = true;
        yield [null, $options, 'types_with_deprecated_options'];
    }

    public static function getDescribeResolvedFormTypeTestData()
    {
        $typeExtensions = [new FormTypeCsrfExtension(new CsrfTokenManager())];
        $parent = new ResolvedFormType(new FormType(), $typeExtensions);

        yield [new ResolvedFormType(new ChoiceType(), [], $parent), ['decorated' => false, 'show_deprecated' => false], 'resolved_form_type_1'];
        yield [new ResolvedFormType(new FormType()), ['decorated' => false, 'show_deprecated' => false], 'resolved_form_type_2'];
        yield [new ResolvedFormType(new FooType(), [], $parent), ['decorated' => false, 'show_deprecated' => true], 'deprecated_options_of_type'];
    }

    public static function getDescribeOptionTestData()
    {
        $parent = new ResolvedFormType(new FormType());
        $options['decorated'] = false;
        $options['show_deprecated'] = false;

        $resolvedType = new ResolvedFormType(new ChoiceType(), [], $parent);
        $options['type'] = $resolvedType->getInnerType();
        $options['option'] = 'choice_translation_domain';
        yield [$resolvedType->getOptionsResolver(), $options, 'default_option_with_normalizer'];

        $resolvedType = new ResolvedFormType(new FooType(), [], $parent);
        $options['type'] = $resolvedType->getInnerType();
        $options['option'] = 'foo';
        yield [$resolvedType->getOptionsResolver(), $options, 'required_option_with_allowed_values'];

        $options['option'] = 'empty_data';
        yield [$resolvedType->getOptionsResolver(), $options, 'overridden_option_with_default_closures'];

        $resolvedType = new ResolvedFormType(new FooType(), [], $parent);
        $options['type'] = $resolvedType->getInnerType();
        $options['option'] = 'bar';
        $options['show_deprecated'] = true;
        yield [$resolvedType->getOptionsResolver(), $options, 'deprecated_option'];

        $resolvedType = new ResolvedFormType(new FooType(), [], $parent);
        $options['type'] = $resolvedType->getInnerType();
        $options['option'] = 'baz';
        yield [$resolvedType->getOptionsResolver(), $options, 'nested_option'];
    }

    abstract protected function getDescriptor();

    abstract protected function getFormat();

    private function getObjectDescription($object, array $options)
    {
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, $options['decorated']);
        $io = new SymfonyStyle(new ArrayInput([]), $output);

        $this->getDescriptor()->describe($io, $object, $options);

        return $output->fetch();
    }

    private function getExpectedDescription($name)
    {
        return file_get_contents($this->getFixtureFilename($name));
    }

    private function getFixtureFilename($name)
    {
        return \sprintf('%s/../../Fixtures/Descriptor/%s.%s', __DIR__, $name, $this->getFormat());
    }
}

class FooType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('foo');
        $resolver->setDefined('bar');
        $resolver->setDeprecated('bar', 'vendor/package', '1.1');
        $resolver->setDefault('empty_data', function (Options $options, $value) {
            $foo = $options['foo'];

            return fn (FormInterface $form) => $form->getConfig()->getCompound() ? [$foo] : $foo;
        });
        $resolver->setAllowedTypes('foo', 'string');
        $resolver->setAllowedValues('foo', ['bar', 'baz']);
        $resolver->setNormalizer('foo', fn (Options $options, $value) => (string) $value);
        $resolver->setOptions('baz', function (OptionsResolver $baz) {
            $baz->setRequired('foo');
            $baz->setDefaults(['foo' => true, 'bar' => true]);
        });
    }
}
