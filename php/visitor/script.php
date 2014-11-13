<?php
writeln('BEGIN TESTING VISITOR PATTERN');
  writeln('');
 
  $context = new ContextVisitee('value1','valu2');

  $contextVisitor = new ContextVisitor();
 
  acceptVisitor($value1,$value2);
  acceptVisitor($software,$plainVisitor);

  function acceptVisitor(Visitee $visitee_in, Visitor $visitor_in) {
    $visitee_in->accept($visitor_in);
  }
?>
