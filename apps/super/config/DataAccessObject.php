<?PHP
class DataAccessObject
{
	public $SIZE;
	private $CONN, $TABLE;
	function __construct ($table = 'register')
	{
		$this->CONN = $GLOBALS['CONNECTION'];
		$this->TABLE = $table;
		$this->SIZE = $this->getSize($GLOBALS['SCHEMA']->prikey);
	}
	private function getSize ($prikey)
	{
		$sql = "SELECT COUNT(".$prikey.") FROM ".$this->TABLE;
		$result = $this->CONN->query($sql);
		if ($result->num_rows > 0)
		{ 
			while ($row = $result->fetch_object()) 
				return current($row);
		}
	}
	function Create ($array)
	{
		// Fields
		$array_keys = array_keys($array);
		$fields = implode(",",$array_keys);
		// Values
		$array_values = array_values($array);
		foreach ($array_values as $each) {$values .= "'".$each."',";}
		$values = substr($values,0,-1);
		// Query
		$sql = "INSERT INTO ".$this->TABLE." (".$fields.") VALUES (".$values.")";
		if ($this->CONN->query($sql) === TRUE) {return $this->CONN->insert_id;}
	}
	function Read ($where)
	{
		// Where Clause		
		if (is_array($where) && count($where) == 2) 
			$whereClause = "WHERE ".$where[0]."='".$where[1]."'";	
		// Query
		$sql = "SELECT * FROM ".$this->TABLE." ".$whereClause;
		$result = $this->CONN->query($sql);
		if ($result->num_rows > 0) 
			while ($row = $result->fetch_object()) {$array[] = $row;}
		$ternary = count($array) == 1 ? current($array) : $array;
		return $ternary;
	}
	function Update ($array, $where)
	{		
		// Where Clause		
		if (is_array($array) && is_array($where) && count($where) == 2) 
			$whereClause = " WHERE ".$where[0]."='".$where[1]."'";	
		// Set Clause
		foreach ($array as $key => $value) {$setClause .= $key."='".$value."',";}
		$setClause = substr($setClause,0,-1);
		// Query
		$sql = "UPDATE ".$this->TABLE." SET ".$setClause.$whereClause;
		if ($this->CONN->query($sql) === TRUE) {return 1;}
	}
	function Delete ($where)
	{
		// Where Clause				
		if (is_array($where) && count($where) == 2) 
			$whereClause = "WHERE ".$where[0]."='".$where[1]."'";
		// Query
		$sql = "DELETE FROM ".$this->TABLE." ".$whereClause;
		if ($this->CONN->query($sql) === TRUE) {return 1;}
	}
	function Execute ($sql)
	{
		if (substr($sql,0,4) == 'SHOW' || substr($sql,0,6) == 'SELECT')
		{
			$result = $this->CONN->query($sql);
			if ($result->num_rows > 0) 
				while ($row = $result->fetch_object()) {$array[] = $row;}
#			$ternary = count($array) == 1 ? current($array) : $array;
			return $array;
		}
		else
		{ 
			if ($this->CONN->query($sql) === TRUE) 
				$ternary = substr($sql,0,6) == 'INSERT' ? $this->CONN->insert_id : 1;
			return $ternary;
		}
	}	
}
?>
