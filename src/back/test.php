<?php

header("Access-Control-Allow-Origin: *");


$data = json_encode(file_get_contents("php://input"), true);
file_put_contents("1.txt",print_r($data,true));
$dbh = new PDO("pgsql:host=localhost;port=5432;dbname=postgres;user=postgres;password=Aa123456");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try
{
    $dbh->beginTransaction();

    $InsertQuery = 'INSERT INTO test."TestTable"
                    (
                        "FirstName",
                        "LastName",
                        "Email",
                        "Subject"
                    )
                    VALUES
                    (
                        :FirstName,
                        :LastName,
                        :Email,
                        :Subject
                    )
                    ON CONFLICT (uuid)
                    DO UPDATE SET
                         "FirstName"=:FirstName,
                         "LastName"=:LastName,
                         "Email"=:Email,
                         "Subject"=:Subject';

    $sth = $dbh->prepare($InsertQuery);

    $sth->bindValue(':FirstName',  val_null($data['firstName']));
    $sth->bindValue(':LastName',   val_null($data['lastName']));
    $sth->bindValue(':Email',      val_null($data['email']));
    $sth->bindValue(':Subject',    val_null($data['subject']));

    $res = $sth->execute();

    $dbh->commit();

    http_response_code(200);
    $data['isSaved'] = true;
}
catch (\PDOException $e)
{
    http_response_code(500);
    $data['error']='Ошибка сохранения данных!';
}

echo json_encode($data,JSON_UNESCAPED_UNICODE);

function val_null($value,$type = null)
{
	switch ($type)
	{
		case null: 		return isset($value)&&$value!=''	?	trim($value)	:null;	break;
		case 'int':		return isset($value)&&$value!==''	?	(int)$value		:null;	break;
		case 'float':	return isset($value)&&$value!=''	?	(float)$value	:null;	break;
		case 'boolean':	{if (isset($value))	{if ($value==0) return 0;if ($value==1) return 1;} else return null;	break;}
		case 'check':	return isset($value)&&$value!=''	?	true			:false;	break;

	}
}

?>