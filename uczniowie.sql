
CREATE DATABASE szkola;

USE szkola;

CREATE TABLE uczniowie(
id_ucz int not null primary key,
nazwisko char(7) not null,
imie char(7) not null,
pesel bigint not null,
adres_ul char(8) not null,
adres_nr char(3) not null,
miasto char(7) not null
);

INSERT INTO uczniowie VALUES
(1, 'Abacki', 'Jan', 95091202012, 'Nocna', '21a', 'Gnieno'),
(2, 'Babacki', 'Tomasz', 96100102013, 'Gwiezdna', '2', 'Gniezno'),
(3, 'Cabacki', 'Jerzy', 95110902056, 'Mierna', '13b', 'Kutno'),
(4, 'Dabacki', 'Tobiasz', 94010398345, 'Bierna', '3', 'Miastko'),
(5, 'Ebacki', 'Adrian', 95010198934, 'Marna', '456', 'Mielno');