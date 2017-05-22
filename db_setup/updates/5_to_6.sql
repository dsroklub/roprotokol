DELETE FROM MemberRightType WHERE member_right = 'wrench';

INSERT INTO MemberRightType (member_right, description, arg, showname, predicate, active)
  VALUES ('wrench', 'har ikke deltaget i vintervedligehold 2015/2016', 2016, 'Mangler vintervedligehold', 'mangle vintervedligehold', 1);

INSERT INTO MemberRightType (member_right, description, arg, showname, predicate, active)
  VALUES ('wrench', 'har ikke deltaget i vintervedligehold 2016/2017', 2017, 'Mangler vintervedligehold', 'mangle vintervedligehold', 1);

UPDATE MemberRights SET argument = 2016 WHERE MemberRight = 'wrench';

