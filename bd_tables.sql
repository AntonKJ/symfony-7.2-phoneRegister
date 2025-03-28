-- Database: phone_register

-- DROP DATABASE IF EXISTS phone_register;

CREATE DATABASE phone_register
    WITH
    OWNER = root
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.utf8'
    LC_CTYPE = 'en_US.utf8'
    LOCALE_PROVIDER = 'libc'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;

-- Table: public.codes

-- DROP TABLE IF EXISTS public.codes;

CREATE TABLE IF NOT EXISTS public.codes
(
    id integer NOT NULL,
    code integer,
    user_id integer,
    datetime text COLLATE pg_catalog."default",
    upd_count text COLLATE pg_catalog."default",
    block_time text COLLATE pg_catalog."default",
    CONSTRAINT codes_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.codes
    OWNER to root;

-- Table: public.users

-- DROP TABLE IF EXISTS public.users;

CREATE TABLE IF NOT EXISTS public.users
(
    id integer NOT NULL,
    phone_id integer NOT NULL,
    mail text COLLATE pg_catalog."default",
    token text COLLATE pg_catalog."default",
    password text COLLATE pg_catalog."default",
    phone numeric,
    CONSTRAINT users_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.users
    OWNER to root;



