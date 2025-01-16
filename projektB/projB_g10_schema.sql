DROP TABLE IF EXISTS playerPosition CASCADE;
DROP TABLE IF EXISTS playerAlias CASCADE;
DROP TABLE IF EXISTS coachCollege CASCADE;
DROP TABLE IF EXISTS coach CASCADE;
DROP TABLE IF EXISTS teamPlayer CASCADE;
DROP TABLE IF EXISTS player CASCADE;
DROP TABLE IF EXISTS team CASCADE;
DROP TABLE IF EXISTS user CASCADE;



CREATE TABLE user (
    userName nvarchar(255),
    CONSTRAINT userName PRIMARY KEY (userName),
    email nvarchar(255),
    userPassword nvarchar(255)
) ENGINE=InnoDB;

CREATE TABLE team (
    ID int not null check(ID > 0),
    CONSTRAINT ID PRIMARY KEY (ID),
    fullName varchar(255),
    ownerName varchar(255),
    creationDate int) ENGINE=InnoDB;

CREATE TABLE player (
    ID int not null check(ID > 0),
    CONSTRAINT ID PRIMARY KEY (ID),
    fullName varchar(255),
    draftYear int,
    birthDate date,
    birthPlace varchar(255),
    _weight float(11,4),
    height float (11,4),
    info varchar(500),
    userName nvarchar(255),
    foreign key (userName) references user (userName)
) ENGINE=InnoDB;

CREATE TABLE teamPlayer (
    team_ID int not null check(team_ID > 0),
    player_ID int not null check(player_ID > 0),
    debute BOOLEAN,
    present BOOLEAN,
    previous BOOLEAN,
    CONSTRAINT pk_teamPlayer primary key(team_ID, player_ID),
    foreign key (team_ID) references team (ID),
    foreign key (player_ID) references player (ID)
) ENGINE=InnoDB;

CREATE TABLE coach(
    ID int not null check(ID > 0),
    CONSTRAINT ID PRIMARY KEY (ID),
    fullName varchar(255),
    birthDate date,
    birthPlace varchar(255),
    info varchar(500),
    team_ID int check(team_ID > 0),
    foreign key (team_ID) references team (ID)
) ENGINE=InnoDB;

CREATE TABLE coachCollege (
    coach_ID int not null check(coach_ID > 0),
    college varchar(255) not null,
    CONSTRAINT pk_coachCollege primary key(coach_ID, college),
    foreign key (coach_ID) references coach(ID)
) ENGINE=InnoDB;

CREATE TABLE playerAlias (
    player_ID int not null check(player_ID > 0),
    alias varchar(255) not null,
    CONSTRAINT pk_playerAlias primary key(player_ID, alias),
    foreign key (player_ID) references player(ID)
) ENGINE=InnoDB;

CREATE TABLE playerPosition (
    player_ID int not null check(player_ID > 0),
    position varchar(255) not null,
    CONSTRAINT pk_playerPosition primary key(player_ID, position),
    foreign key (player_ID) references player(ID)
) ENGINE=InnoDB;

SHOW TABLES;