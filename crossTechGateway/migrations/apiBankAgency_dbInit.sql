create schema api_purchasecode;
alter schema api_purchasecode owner to lcruz;

create sequence api_purchasecode.seq_aquirers start with 1 increment 1 minvalue 1 cache 1;
create table api_purchasecode.aquirers (
    id     bigint       not null default nextval('api_purchasecode.seq_aquirers') primary key,
    name   varchar(100) not null,
    code   varchar(10)  not null,
    active bool         not null default false
);
alter table api_purchasecode.aquirers owner to lcruz;

create sequence api_purchasecode.seq_merchants start with 1 increment 1 minvalue 1 cache 1;
create table api_purchasecode.merchants (
    id             bigint      not null default nextval('api_purchasecode.seq_merchants') primary key,
    aquirer_id     bigint      not null references api_purchasecode.aquirer(id),
    mid            varchar(20) not null,
    name           varchar(100) not null,
    country        varchar(100),
    state          varchar(2),
    city           varchar(100),
    active         bool default false not null,
);
alter table api_purchasecode.merchants owner to lcruz;


create sequence api_purchasecode.seq_terminals start with 1 increment 1 minvalue 1 cache 1;
create table api_purchasecode.terminals (
    id          bigint      not null default nextval('api_purchasecode.seq_terminals') primary key,
    merchant_id bigint not null references api_purchasecode.merchant(id),
    tid         varchar(20) not null,
    description varchar(255),
    active      bool default false not null
);
alter table api_purchasecode.terminals owner to lcruz;

create sequence api_purchasecode.seq_purchasecodes start with 1 increment 1 minvalue 1 cache 1;
create table api_purchasecode.purchasecodes (
    id                   bigint      not null default nextval('api_purchasecode.seq_purchasecode') primary key,
    partner_id           bigint      not null references api_security.partners(id),
    user_id              bigint      not null references api_security.users(id),
    request_id           varchar(100) not null,
    unique_customer_id  varchar(100),
    amount               numeric(12,2) not null,
    currency             varchar(3) not null,
    lifetime             numeric(3,0) not null default 300,
    merchant_id          bigint,
    terminal_id          bigint,
    reference_id         varchar(100),
    purchase_code        varchar(100),
    creation_date        timestamp not null,
    expire_date          timestamp,
    status               varchar(1) default 'P' not null
);
alter table api_purchasecode.purchasecodes owner to lcruz;