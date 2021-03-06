CREATE USER apacheauth@localhost IDENTIFIED BY 'XXXX';
GRANT ALL PRIVILEGES ON roprotokol.authentication  TO apacheauth@'localhost';
GRANT SELECT ON roprotokol.Member  TO apacheauth@'localhost';
GRANT SELECT ON roprotokol.MemberRights  TO apacheauth@'localhost';

-- for testing
INSERT INTO MemberRights VALUE(6270,'developer',NOW(),'root')

use roprotokol

CREATE USER pw@localhost IDENTIFIED BY 'XXXX';
GRANT SELECT ON roprotokol.Member  TO pw@'localhost';
GRANT ALL PRIVILEGES ON roprotokol.authentication  TO pw@'localhost';
