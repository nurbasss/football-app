-----------------------CREATE TABLES----------------------------------------
CREATE TABLE matches (
  matchId int NOT NULL,
  homeTeam int DEFAULT NULL,
  awayTeam int DEFAULT NULL,
  date date NOT NULL,
  FTHG int NOT NULL,
  FTAG int NOT NULL,
  result char(1) NOT NULL,
  HTHG int NOT NULL,
  HTAG int NOT NULL,
  halfResult char(1) NOT NULL,
  hShot int NOT NULL,
  aShot int NOT NULL,
  hShotTar int NOT NULL,
  aShotTar int NOT NULL,
  hFouls int NOT NULL,
  aFouls int NOT NULL,
  hCorners int NOT NULL,
  aCorners int NOT NULL,
  hYellow int NOT NULL,
  aYellow int NOT NULL,
  hRed int NOT NULL,
  aRed int NOT NULL
) ;

CREATE TABLE teams (
  teamId int NOT NULL,
  teamName varchar(30) NOT NULL
) ;

CREATE TABLE users (
  userId int NOT NULL,
  email varchar(40) NOT NULL,
  pass long NOT NULL,
  team int NOT NULL
) ;
CREATE TABLE user_matches (
  userVal int(11) NOT NULL,
  matchVal int(11) NOT NULL,
  fav char(1) DEFAULT 'N'
);

ALTER TABLE matches
  ADD PRIMARY KEY (matchId);

ALTER TABLE teams
  ADD PRIMARY KEY (teamId);

ALTER TABLE users
  ADD PRIMARY KEY (userId);

ALTER TABLE user_matches
  ADD CONSTRAINT user_matches_ibfk_1 FOREIGN KEY (userVal) REFERENCES users (userId);
ALTER TABLE user_matches
  ADD CONSTRAINT user_matches_ibfk_2 FOREIGN KEY (matchVal) REFERENCES matches (matchId);

ALTER TABLE users
  ADD CONSTRAINT users_ibfk_1 FOREIGN KEY (team) REFERENCES teams (teamId);

ALTER TABLE matches
  ADD CONSTRAINT matches_ibfk_1 FOREIGN KEY (homeTeam) REFERENCES teams (teamId);
ALTER TABLE matches
  ADD CONSTRAINT matches_ibfk_2 FOREIGN KEY (awayTeam) REFERENCES teams (teamId);
  

-------------------TRIGGERS (SEQUENCES)-------------------------------------
--to auto increment userid in users table
CREATE SEQUENCE users_seq START WITH 2; 
CREATE OR REPLACE TRIGGER users_bir 
BEFORE INSERT ON users 
FOR EACH ROW

BEGIN
  SELECT users_seq.NEXTVAL
  INTO   :new.userid
  FROM   dual;
END;
-------------------------------------------
--to auto increment matchid in matches table
CREATE SEQUENCE matches_seq START WITH 11244; 
CREATE OR REPLACE TRIGGER matches_bir 
BEFORE INSERT ON matches 
FOR EACH ROW

BEGIN
  SELECT matches_seq.NEXTVAL
  INTO   :new.matchid
  FROM   dual;
END;
-------------------------------------------
--to auto increment matchid in matches table
CREATE SEQUENCE teams_seq START WITH 100; 
CREATE OR REPLACE TRIGGER teams_bir 
BEFORE INSERT ON teams 
FOR EACH ROW

BEGIN
  SELECT teams_seq.NEXTVAL
  INTO   :new.teamId
  FROM   dual;
END;

CREATE OR REPLACE TRIGGER user_matches_after_insert_trig
AFTER INSERT ON user_matches FOR EACH ROW
BEGIN
	insert into user_matches_log
	(operation_date, old_userVal, new_userVal, old_matchVal, new_matchVal, old_fav, new_fav, action, author)
	 VALUES(SYSDATE, :OLD.userVal, :NEW.userVal, :OLD.matchVal, :NEW.matchVal, :OLD.fav, :NEW.fav, 'INSERT', USER);
END;
---------------------------------------------------

CREATE OR REPLACE TRIGGER user_matches_after_update_trig
AFTER UPDATE ON user_matches FOR EACH ROW
BEGIN
	insert into user_matches_log
	(operation_date, old_userVal, new_userVal, old_matchVal, new_matchVal, old_fav, new_fav, action, author)
	 VALUES(SYSDATE, :OLD.userVal, :NEW.userVal, :OLD.matchVal, :NEW.matchVal, :OLD.fav, :NEW.fav, 'UPDATE', USER);
END;
----------------------------------------------------------

CREATE OR REPLACE TRIGGER user_matches_after_delete_trig
AFTER DELETE ON user_matches FOR EACH ROW
BEGIN
	insert into user_matches_log
	(operation_date, old_userVal, new_userVal, old_matchVal, new_matchVal, old_fav, new_fav, action, author)
	 VALUES(SYSDATE, :OLD.userVal, :NEW.userVal, :OLD.matchVal, :NEW.matchVal, :OLD.fav, :NEW.fav, 'DELETE', USER);
END;

---------------------PROCEDURES AND FUNCTIONS----------------------
create or replace procedure addUser(p_userid in users.userid%TYPE, p_email in users.email%TYPE, p_pass in users.pass%TYPE, p_team in users.team%TYPE)
as
begin
   INSERT INTO users (userid, email, pass, team) VALUES (p_userid, p_email, p_pass,p_team);
   
end;
-------------------------------------------
create or replace function checkUserRegister(p_email in users.email%TYPE)
return number
is 
checker number;
counter NUMBER;
begin
    SELECT COUNT(*) INTO counter FROM users WHERE email = p_email;
    if(counter>0) then checker:= 1;
    else checker:=0;
    end if;
    return checker;
end checkUserRegister;
-------------------------------------------
create or replace procedure resetPassword(p_email in users.email%TYPE, p_pass in users.pass%TYPE)
is
begin
    UPDATE users SET pass = p_pass WHERE email= p_email;
end;
-------------------------------------------
create or replace procedure addUserMatch(p_userVal user_matches.userval%type, p_matchVal user_matches.matchval%type)
is
begin
    INSERT INTO user_matches (userVal, matchVal) VALUES (p_userVal, p_matchVal);
end;
-------------------------------------------
create or replace procedure deleteUserMatch(p_userVal user_matches.userval%type, p_matchVal user_matches.matchval%type)
is
begin
    DELETE FROM user_matches WHERE userVal= p_userVal AND matchVal= p_matchVal;
end;
-------------------------------------------
create or replace procedure changeTeam(p_email in users.email%TYPE, p_team in users.team%TYPE)
is
begin
    UPDATE users SET team = p_team WHERE email= p_email;
end;
-------------------------------------------
create or replace procedure makeFavourite(p_userVal user_matches.userval%type, p_matchVal user_matches.matchval%type)
is 
begin
    UPDATE user_matches SET fav = 'Y' WHERE userVal = p_userVal AND matchVal = p_matchVal;
end;
-------------------------------------------
create or replace procedure makeUnfavourite(p_userVal user_matches.userval%type, p_matchVal user_matches.matchval%type)
is 
begin
    UPDATE user_matches SET fav = 'N' WHERE userVal = p_userVal AND matchVal = p_matchVal;
end;
-------------------------------------------
CREATE OR REPLACE FUNCTION highestScoringMatch(p_userval IN user_matches.userval%TYPE)
RETURN NUMBER 
IS
v_matchid number;
temp_res int :=0;
c_matchval user_matches.matchval%TYPE;
CURSOR u_matches is SELECT * from matches m join user_matches u on m.matchId=u.matchval where u.userval=p_userval;
r_matches u_matches%ROWTYPE;
BEGIN
    open u_matches;
    LOOP 
    FETCH u_matches into r_matches;
        EXIT WHEN u_matches%notfound;
        if((r_matches.FTHG + r_matches.FTAG) > temp_res) 
            then 
                temp_res:=(r_matches.FTHG + r_matches.FTAG);
                v_matchid:=r_matches.matchId;
        end if;
    END LOOP;
    return v_matchid;
END;
--------------------------------------------

CREATE OR REPLACE FUNCTION mostCardsMatch(p_userval IN user_matches.userval%TYPE)
RETURN NUMBER 
IS
v_matchid number;
temp_res int :=0;
c_matchval user_matches.matchval%TYPE;
CURSOR u_matches is SELECT * from matches m join user_matches u on m.matchId=u.matchval where u.userval=p_userval;
r_matches u_matches%ROWTYPE;
BEGIN
    open u_matches;
    LOOP 
    FETCH u_matches into r_matches;
        EXIT WHEN u_matches%notfound;
        if((r_matches.HYELLOW + r_matches.AYELLOW + r_matches.HRED + r_matches.ARED) > temp_res) 
            then 
                temp_res:=(r_matches.HYELLOW + r_matches.AYELLOW + r_matches.HRED + r_matches.ARED);
                v_matchid:=r_matches.matchId;
        end if;
    END LOOP;
    return v_matchid;
END;


----------------
CREATE OR REPLACE FUNCTION allMostCardsMatch
RETURN NUMBER 
IS
v_matchid number;
temp_res int :=0;
c_matchval user_matches.matchval%TYPE;
CURSOR u_matches is SELECT * from matches;
r_matches u_matches%ROWTYPE;
BEGIN
    open u_matches;
    LOOP 
    FETCH u_matches into r_matches;
        EXIT WHEN u_matches%notfound;
        if((r_matches.HYELLOW + r_matches.AYELLOW + r_matches.HRED + r_matches.ARED) > temp_res) 
            then 
                temp_res:=(r_matches.HYELLOW + r_matches.AYELLOW + r_matches.HRED + r_matches.ARED);
                v_matchid:=r_matches.matchId;
        end if;
    END LOOP;
    return v_matchid;
END;
------------------------------
CREATE OR REPLACE FUNCTION allHighestScoringMatch
RETURN NUMBER 
IS
v_matchid number;
temp_res int :=0;
c_matchval user_matches.matchval%TYPE;
CURSOR u_matches is SELECT * from matches;
r_matches u_matches%ROWTYPE;
BEGIN
    open u_matches;
    LOOP 
    FETCH u_matches into r_matches;
        EXIT WHEN u_matches%notfound;
        if((r_matches.FTHG + r_matches.FTAG) > temp_res) 
            then 
                temp_res:=(r_matches.FTHG + r_matches.FTAG);
                v_matchid:=r_matches.matchId;
        end if;
    END LOOP;
    return v_matchid;
END;

-------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION allMostCornersMatch
RETURN NUMBER 
IS
v_matchid number;
temp_res int :=0;
c_matchval user_matches.matchval%TYPE;
CURSOR u_matches is SELECT * from matches;
r_matches u_matches%ROWTYPE;
BEGIN
    open u_matches;
    LOOP 
    FETCH u_matches into r_matches;
        EXIT WHEN u_matches%notfound;
        if((r_matches.HCORNERS + r_matches.ACORNERS) > temp_res) 
            then 
                temp_res:=(r_matches.HCORNERS + r_matches.ACORNERS);
                v_matchid:=r_matches.matchId;
        end if;
    END LOOP;
    return v_matchid;
END;


------------------------------PACKAGES-----------------------------------
CREATE OR REPLACE PACKAGE user_auth AS 
   PROCEDURE addUser(p_userid in users.userid%TYPE, 
                     p_email in users.email%TYPE,
                     p_pass in users.pass%TYPE, 
                     p_team in users.team%TYPE);
                     
   FUNCTION checkUserRegister(p_email in users.email%TYPE) RETURN number;        
   PROCEDURE resetPassword(p_email in users.email%TYPE, 
                           p_pass in users.pass%TYPE);
END user_auth; 

CREATE OR REPLACE PACKAGE BODY user_auth AS
    procedure addUser(p_userid in users.userid%TYPE, p_email in users.email%TYPE, p_pass in users.pass%TYPE, p_team in users.team%TYPE)
    as
    begin
     INSERT INTO users (userid, email, pass, team) VALUES (p_userid, p_email, p_pass,p_team);   
    end addUser;    
    
    function checkUserRegister(p_email in users.email%TYPE)
    return number
    is 
    checker number;
    counter NUMBER;
    begin
        SELECT COUNT(*) INTO counter FROM users WHERE email = p_email;
        if(counter>0) then checker:= 1;
        else checker:=0;
        end if;
    return checker;
    end checkUserRegister;
    
    procedure resetPassword(p_email in users.email%TYPE, p_pass in users.pass%TYPE)
    is
    begin
        UPDATE users SET pass = p_pass WHERE email= p_email;
    end resetPassword;
END user_auth;
-------------------------------------------------------------------

CREATE OR REPLACE PACKAGE user_game AS 
   PROCEDURE addUserMatch(p_userVal user_matches.userval%type, 
                          p_matchVal user_matches.matchval%type);
                          
   procedure deleteUserMatch(p_userVal user_matches.userval%type, 
                             p_matchVal user_matches.matchval%type);
   
   procedure changeTeam(p_email in users.email%TYPE,
                        p_team in users.team%TYPE);
   
   procedure makeFavourite(p_userVal user_matches.userval%type,
                           p_matchVal user_matches.matchval%type);
   
   procedure makeUnfavourite(p_userVal user_matches.userval%type,
                             p_matchVal user_matches.matchval%type);
   
END user_game; 

CREATE OR REPLACE PACKAGE BODY user_game AS
    procedure addUserMatch(p_userVal user_matches.userval%type, p_matchVal user_matches.matchval%type)
    is
    begin
        INSERT INTO user_matches (userVal, matchVal) VALUES (p_userVal, p_matchVal);
    end addUserMatch;

    procedure deleteUserMatch(p_userVal user_matches.userval%type, p_matchVal user_matches.matchval%type)
    is
    begin
        DELETE FROM user_matches WHERE userVal= p_userVal AND matchVal= p_matchVal;
    end deleteUserMatch;

    procedure changeTeam(p_email in users.email%TYPE, p_team in users.team%TYPE)
    is
    begin
        UPDATE users SET team = p_team WHERE email= p_email;
    end changeTeam;

    procedure makeFavourite(p_userVal user_matches.userval%type, p_matchVal user_matches.matchval%type)
    is 
    begin
        UPDATE user_matches SET fav = 'Y' WHERE userVal = p_userVal AND matchVal = p_matchVal;
    end makeFavourite;

    procedure makeUnfavourite(p_userVal user_matches.userval%type, p_matchVal user_matches.matchval%type)
    is 
    begin
        UPDATE user_matches SET fav = 'N' WHERE userVal = p_userVal AND matchVal = p_matchVal;
    end makeUnfavourite;
END user_game; 

----------------------------------------------------------------
CREATE OR REPLACE PACKAGE user_game_stats AS 
    FUNCTION highestScoringMatch(p_userval IN user_matches.userval%TYPE)
    RETURN NUMBER;
    
    FUNCTION mostCardsMatch(p_userval IN user_matches.userval%TYPE)
    RETURN NUMBER;
END user_game_stats; 

CREATE OR REPLACE PACKAGE BODY user_game_stats AS
    FUNCTION highestScoringMatch(p_userval IN user_matches.userval%TYPE)
    RETURN NUMBER 
    IS
    v_matchid number;
    temp_res int :=0;
    c_matchval user_matches.matchval%TYPE;
    CURSOR u_matches is SELECT * from matches m join user_matches u on m.matchId=u.matchval where u.userval=p_userval;
    r_matches u_matches%ROWTYPE;
    BEGIN
        open u_matches;
        LOOP 
        FETCH u_matches into r_matches;
            EXIT WHEN u_matches%notfound;
            if((r_matches.FTHG + r_matches.FTAG) > temp_res) 
                then 
                    temp_res:=(r_matches.FTHG + r_matches.FTAG);
                    v_matchid:=r_matches.matchId;
            end if;
        END LOOP;
        return v_matchid;
    END highestScoringMatch;
    
    FUNCTION mostCardsMatch(p_userval IN user_matches.userval%TYPE)
    RETURN NUMBER 
    IS
    v_matchid number;
    temp_res int :=0;
    c_matchval user_matches.matchval%TYPE;
    CURSOR u_matches is SELECT * from matches m join user_matches u on m.matchId=u.matchval where u.userval=p_userval;
    r_matches u_matches%ROWTYPE;
    BEGIN
        open u_matches;
        LOOP 
        FETCH u_matches into r_matches;
            EXIT WHEN u_matches%notfound;
            if((r_matches.HYELLOW + r_matches.AYELLOW + r_matches.HRED + r_matches.ARED) > temp_res) 
                then 
                    temp_res:=(r_matches.HYELLOW + r_matches.AYELLOW + r_matches.HRED + r_matches.ARED);
                    v_matchid:=r_matches.matchId;
            end if;
        END LOOP;
        return v_matchid;
    END mostCardsMatch;

END user_game_stats; 

-------------------------------------------------------------------------------------------

CREATE OR REPLACE PACKAGE all_game_stats AS 
    
    FUNCTION allMostCardsMatch RETURN NUMBER;
    
    FUNCTION allHighestScoringMatch RETURN NUMBER;
    
    FUNCTION allMostCornersMatch RETURN NUMBER;
    
END all_game_stats;

CREATE OR REPLACE PACKAGE BODY all_game_stats AS
    FUNCTION allMostCardsMatch
    RETURN NUMBER 
    IS
    v_matchid number;
    temp_res int :=0;
    c_matchval user_matches.matchval%TYPE;
    CURSOR u_matches is SELECT * from matches;
    r_matches u_matches%ROWTYPE;
    BEGIN
        open u_matches;
        LOOP 
        FETCH u_matches into r_matches;
            EXIT WHEN u_matches%notfound;
            if((r_matches.HYELLOW + r_matches.AYELLOW + r_matches.HRED + r_matches.ARED) > temp_res) 
                then 
                    temp_res:=(r_matches.HYELLOW + r_matches.AYELLOW + r_matches.HRED + r_matches.ARED);
                    v_matchid:=r_matches.matchId;
            end if;
        END LOOP;
        return v_matchid;
    END;
    
    FUNCTION allHighestScoringMatch
    RETURN NUMBER 
    IS
    v_matchid number;
    temp_res int :=0;
    c_matchval user_matches.matchval%TYPE;
    CURSOR u_matches is SELECT * from matches;
    r_matches u_matches%ROWTYPE;
    BEGIN
        open u_matches;
        LOOP 
        FETCH u_matches into r_matches;
            EXIT WHEN u_matches%notfound;
            if((r_matches.FTHG + r_matches.FTAG) > temp_res) 
                then 
                    temp_res:=(r_matches.FTHG + r_matches.FTAG);
                    v_matchid:=r_matches.matchId;
            end if;
        END LOOP;
        return v_matchid;
    END;
    
    FUNCTION allMostCornersMatch
    RETURN NUMBER 
    IS
    v_matchid number;
    temp_res int :=0;
    c_matchval user_matches.matchval%TYPE;
    CURSOR u_matches is SELECT * from matches;
    r_matches u_matches%ROWTYPE;
    BEGIN
        open u_matches;
        LOOP 
        FETCH u_matches into r_matches;
            EXIT WHEN u_matches%notfound;
            if((r_matches.HCORNERS + r_matches.ACORNERS) > temp_res) 
                then 
                    temp_res:=(r_matches.HCORNERS + r_matches.ACORNERS);
                    v_matchid:=r_matches.matchId;
            end if;
        END LOOP;
        return v_matchid;
    END;
    
END all_game_stats;

----------------------Dynamic SQL--------------------------------------
--procedure to create logging table for  provided table

CREATE OR REPLACE PROCEDURE create_logging(p_table_name in VARCHAR2) IS
v_dynamic_stmt1 VARCHAR2(220);
v_dynamic_stmt2 VARCHAR2(250);
v_col_name VARCHAR2(50);
v_data_type VARCHAR2(50);
v_data_length INTEGER;
CURSOR col_name is 
      SELECT column_name, data_type, data_length
        FROM USER_TAB_COLUMNS WHERE table_name = p_table_name;
BEGIN
v_dynamic_stmt1 := 'CREATE TABLE ' || p_table_name ||'_LOG'
                    || '( id NUMBER, ' 
                    || 'OPERATION_DATE DATE, '
                    || 'ACTION VARCHAR(255), '
                    || 'AUTHOR VARCHAR(255) )';
EXECUTE IMMEDIATE v_dynamic_stmt1;
OPEN col_name; 
   LOOP 
   FETCH col_name into v_col_name, v_data_type, v_data_length; 
      EXIT WHEN col_name%notfound; 
       if(v_data_length != null) then
        v_dynamic_stmt2:= 'ALTER TABLE '||p_table_name ||'_LOG'||
                          ' ADD('||' NEW_'||v_col_name||' '||v_data_type||'('||v_data_length||'),'
                                ||' OLD_'||v_col_name||' '||v_data_type||'('||v_data_length||'))';
        else
             v_dynamic_stmt2:= 'ALTER TABLE '||p_table_name ||'_LOG'||
                          ' ADD('||' NEW_'||v_col_name||' '||v_data_type||','
                                ||' OLD_'||v_col_name||' '||v_data_type||')';
        end if;
        EXECUTE IMMEDIATE v_dynamic_stmt2;
        
   END LOOP;
CLOSE col_name;
END create_logging;
