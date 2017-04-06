CREATE USER apache@localhost IDENTIFIED BY 'XXXX';
GRANT SELECT ON roprotokol.authentication  TO apache@'localhost';
GRANT SELECT ON roprotokol.Member  TO apache@'localhost';

CREATE USER pw@localhost IDENTIFIED BY 'XXXX';
GRANT SELECT ON roprotokol.Member  TO pw@'localhost';
GRANT ALL PRIVILEGES ON roprotokol.authentication  TO pw@'localhost';
