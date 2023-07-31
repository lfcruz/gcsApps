create schema api_security;
alter schema api_security owner to lcruz;

create sequence api_security.seq_partners start with 1 increment 1 minvalue 1 cache 1;
create table api_security.partners (
    id     bigint       not null default nextval('api_security.seq_partners') primary key,
    name   varchar(100) not null,
    code   varchar(10)  not null,
    base   bool not null default true,
    parent bigint,
    active bool         not null default false
);
alter table api_security.partners owner to lcruz;

create sequence api_security.seq_users start with 1 increment 1 minvalue 1 cache 1;
create table api_security.users (
    id             bigint      not null default nextval('seq_users') primary key,
    partner_id     BIGINT      not null references partners(id),
    username       varchar(20) not null,
    secured        varchar(100) not null,
    active         bool default false not null,
    salted         varchar(64) not null,
    email          varchar(100) not null,
    secret         varchar(100) not null
);
alter table api_security.users owner to lcruz;


create sequence api_security.seq_services start with 1 increment 1 minvalue 1 cache 1;
create table api_security.services (
    id          bigint      not null default nextval('seq_services') primary key,
    api_tag     varchar(50) not null,
    description varchar(100),
    active      bool default false not null
);
alter table api_security.services owner to lcruz;

create sequence api_security.seq_functions start with 1 increment 1 minvalue 1 cache 1;
create table api_security.functions (
    id          bigint      not null default nextval('seq_functions') primary key,
    service_id  bigint      not null references services(id),
    api_tag     varchar(50) not null,
    description varchar(100),
    secure      bool default true not null,
    active      bool default false not null
);
alter table api_security.functions owner to lcruz;


create sequence api_security.seq_roles start with 1 increment 1 minvalue 1 cache 1;
create table api_security.roles (
    id          bigint      not null default nextval('seq_roles') primary key,
    api_tag     varchar(50) not null,
    description varchar(100)
);
alter table api_security.roles owner to lcruz;


create sequence api_security.seq_roles_definition start with 1 increment 1 minvalue 1 cache 1;
create table api_security.roles_definition(
    id           bigint not null default nextval('seq_roles_definition') primary key,
    roles_id     bigint not null references roles(id),
    functions_id bigint not null references functions(id)
);
alter table api_security.roles_definition owner to lcruz;

create sequence api_security.seq_users_roles start with 1 increment 1 minvalue 1 cache 1;
create table api_security.users_roles(
    id      bigint not null default nextval('seq_users_roles') primary key,
    user_id bigint not null references users(id),
    role_id bigint not null references roles(id)
);
alter table api_security.users_roles owner to lcruz;

create sequence api_security.seq_authentication_keys start with 1 increment 1 minvalue 1 cache 1;
create table api_security.authentication_keys(
    id         bigint not null default nextval('seq_authentication_keys') primary key,
    key_name   varchar(50) not null unique,
    key_weight smallint not null unique
);
alter table api_security.authentication_keys owner to lcruz;

create sequence api_security.seq_service_authentication_keys start with 1 increment 1 minvalue 1 cache 1;
create table api_security.service_authentication_keys(
    id         bigint not null default nextval('seq_service_authentication_keys') primary key,
    key_code   varchar(50),
    service_id bigint not null references services(id)
);

insert into api_security.functions values (default, 1, 'get-token', 'Generate jwt api token', true);
insert into api_security.functions values (default, 1, 'get-jwk', 'Get jwk signing validation', true);
insert into api_security.functions values (default, 1, 'set-password', 'Cambio password usuario', true);
