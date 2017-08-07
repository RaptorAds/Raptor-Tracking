Declare @TEMP table (ip varchar(300), postback varchar(300), visit varchar(300), campaign varchar(300), publisherID varchar(100), clickID varchar(100))
Declare @IPTable table (ip varchar(300), dupe varchar(10))
Declare @time datetime
Declare @OS varchar(100)
Declare @Device varchar(100)
Declare @Version varchar(100)
Declare @Brand varchar(100)
Declare @Model varchar(100)
Declare @Browser varchar(100)
Declare @ClickID varchar(100)
insert into @IPTable 
SELECT [IP], COUNT(*) TotalCount
FROM dec16
GROUP BY [IP] HAVING COUNT(*) > 1
DECLARE @IP varchar(300)
DECLARE db_cursor CURSOR FOR
	SELECT ip
	FROM @IPTable
OPEN db_cursor   
FETCH NEXT FROM db_cursor INTO @IP
WHILE @@FETCH_STATUS = 0   
BEGIN
	--Begin Loop (1)
	--Begin Loop (2)
	DECLARE db_cursor2 CURSOR FOR
	SELECT [Click ID]
	FROM dec16
	Where [IP] = @IP
	OPEN db_cursor2   
	FETCH NEXT FROM db_cursor2 INTO @ClickID
	WHILE @@FETCH_STATUS = 0   
	BEGIN
		Set @time = (select Convert(datetime, [Postback Timestamp], 103) from dec16 where [Click ID] = @ClickID)
		Set @OS =  (select [OS] from dec16 where [Click ID] = @ClickID)
		Set @Device =  (select [Device] from dec16 where [Click ID] = @ClickID)
		Set @Version =  (select [Browser Version] from dec16 where [Click ID] = @ClickID)
		Set @Brand =  (select [Brand] from dec16 where [Click ID] = @ClickID)
		Set @Model =  (select [Model] from dec16 where [Click ID] = @ClickID)
		Set @Browser =  (select [Model] from dec16 where [Click ID] = @ClickID)
		
		Insert into @TEMP (ip, postback, visit, campaign, publisherID, clickID)
		select [IP], [Postback Timestamp], [Visit Timestamp], Campaign, [Publisher ID], [Click ID] from dec16 
		where 
			[IP] = @IP
			and [OS] = @OS
			and [Device] = @Device
			and [Browser Version] = @Version
			and [Brand] = @Brand
			and [Model] = @Model
			and [Browser] = @Browser
			and abs(DateDiff(ss,Convert(datetime, [Postback Timestamp],103), Convert(datetime,@time,103))) <= 60 and abs(DateDiff(ss,Convert(datetime, [Postback Timestamp],103), Convert(datetime,@time,103))) <> 0
	
	--End Loop (2)
	FETCH NEXT FROM db_cursor2 INTO @ClickID
	END   
	CLOSE db_cursor2   
	DEALLOCATE db_cursor2

	--End Loop (1)
	FETCH NEXT FROM db_cursor INTO @IP
END   
CLOSE db_cursor   
DEALLOCATE db_cursor

select * from @TEMP
