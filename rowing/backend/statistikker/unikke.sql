SELECT COUNT(distinct member_id) FROM TripMember WHERE YEAR(CreatedDate)=YEAR(NOW());

