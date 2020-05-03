create database IF NOT EXISTS lenvendo;

use lenvendo;

create table bookmark
(
    id           int auto_increment
        primary key,
    url          varchar(255)            not null,
    favicon      varchar(255)            not null,
    title        varchar(255)            not null,
    description  varchar(255) default '' null,
    keywords     varchar(255)            null,
    date_created datetime                not null,
    password     varchar(255)            null,
    constraint bookmark_url_uindex
        unique (url)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;