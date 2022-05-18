INSERT INTO MemberRights(member_id,MemberRight,argument,created_by)
SELECT Member.id,'wrench','2022',1445
FROM Member,worker LEFT JOIN
(SELECT member_id,SUM(hours) as h from worklog
  WHERE
  ((YEAR(start_time)=YEAR(NOW()) AND (MONTH(start_time)>10 OR MONTH(NOW())<11)) OR (YEAR(start_time)=YEAR(NOW())-1 AND MONTH(start_time)>10 AND MONTH(NOW())<10))
GROUP BY worklog.member_id)
as w ON worker.member_id=w.member_id
    WHERE worker.member_id=Member.id AND requirement-IFNULL(h,0)>5;
