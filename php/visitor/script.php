<?php
	//visitee's
	require_once("visitee.class.php");
	require_once("contextVisitee.class.php");
	
	//visitors
	require_once("visitor.class.php");
	require_once("contextVisitor.vlass.php");
	
  $context = new ContextVisitee('value1','value2');

  $contextVisitor = new ContextVisitor();
 
  acceptVisitor($value1,$value2);
  acceptVisitor($software,$plainVisitor);

  function acceptVisitor(Visitee $visitee_in, Visitor $visitor_in) {
    $visitee_in->accept($visitor_in);
  }
?>
