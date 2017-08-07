use zvolumn
Declare @TEMP table (ip varchar(300), postback varchar(300), visit varchar(300), campaign varchar(300), publisherID varchar(100), clickID varchar(100))
Declare @time datetime
Declare @key varchar(300)
Declare @ClickID varchar(100)
Declare @IPTable table (ip varchar(300), dupe varchar(10))
insert into @IPTable 
SELECT [OS] + [OS Version] + Device + Browser + Brand + Model + [Mobile Carrier] ,COUNT(*) TotalCount
FROM dec16
GROUP BY
	 [OS]
	, [OS Version]
	, [Device] 
	, [Browser]
	, [Brand] 
	, [Model] 
	, [Mobile Carrier] 
HAVING COUNT(*) > 1
DECLARE db_cursor CURSOR FOR
	SELECT [Click ID]
	FROM dec16
OPEN db_cursor   
FETCH NEXT FROM db_cursor INTO @ClickID
WHILE @@FETCH_STATUS = 0   
BEGIN
	--Begin Loop (1)
	select @time = Convert(datetime, [Postback Timestamp],103), @ip = [IP] from dec16 where [Click ID] = @ClickID
	insert into @TEMP (ip, postback, visit, campaign, publisherID, clickID)
	select [IP], [Postback Timestamp],[Visit Timestamp], Campaign, [Publisher ID], [Click ID] 
	from dec16 
	where 
		[Click ID] <> @ClickID 
		and abs(DateDiff(ss,Convert(datetime, [Postback Timestamp],103), @time)) <= 60
		and [IP] = @ip

	--End Loop (1)
	FETCH NEXT FROM db_cursor INTO @ClickID
END   
CLOSE db_cursor   
DEALLOCATE db_cursor

select * from @TEMP
