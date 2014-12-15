// <?php
// abstract class Element
// {
    // public function accept(Visitor $visitor)
    // {
        // // ... Call visitFoo, etc, here
        // $visitMethods = get_class_methods($visitor);
        // $elementClass = get_class($this);
//  
        // foreach ($visitMethods as $method) {
//  
            // // we've found the visitation method for this class type
            // if ('visit' . $elementClass == $method) {
//  
                // // visit the method and exit
                // $visitor->{'visit' . $elementClass}($this);
                // return;
            // }
        // }
//  
        // // If no visitFoo, etc, call a default algorithm
        // $visitor->defaultVisit($this);
    // }
// }
//  
// class Foo extends Element
// {
    // // ... some functionality specific to Foo
// }
//  
// interface Visitor
// {
    // public function defaultVisit(Element $element);
// }
//  
// class UpdateVisitor implements Visitor
// {
    // public function visitFoo(Foo $theElement)
    // {
        // // ... 'update' $theElement of type Foo
    // }
//  
    // public function defaultVisit(Element $element)
    // {
        // $elementClass = get_class($element);
        // $thisClass = get_class($this);
        // throw new Exception("Visitor method " . $thisClass . "::visit" . $elementClass . "(" . $elementClass . ") is not implemented!");
    // }
// }
//  
// $updateVisitor = new UpdateVisitor();
// $element = new Foo();
// $element->visit($updateVisitor);
// ?>