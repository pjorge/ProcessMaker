CREATE TRIGGER APPLICATION_UPDATE BEFORE UPDATE ON APPLICATION

FOR EACH ROW

BEGIN
  DECLARE APP_STATUS VARCHAR(32);
  SELECT APPLICATION.APP_STATUS into @APP_STATUS FROM APPLICATION WHERE APP_UID = NEW.APP_UID LIMIT 1; 

  IF(OLD.APP_STATUS<>NEW.APP_STATUS) THEN

    SET @APP_STATUS = NEW.APP_STATUS;
    UPDATE APP_CACHE_VIEW SET APP_STATUS = @APP_STATUS WHERE APP_UID = NEW.APP_UID;  

  END IF;
  
  IF(OLD.APP_DATA<>NEW.APP_DATA) THEN

    UPDATE APP_CACHE_VIEW SET APP_UPDATE_DATE = NOW() WHERE APP_UID = NEW.APP_UID;  

  END IF;

END
