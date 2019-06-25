# unless

- Aliases: `if_not`, `ifnot`, `ifnt`, `?!`
- Syntax: `unless <condition> <code> [otherwise <code>]`

Same as [if](if), except that the condition mustn't be met.

    local better_plans no;
    unless better_plans {
        print "Conditionals it is!";
    };

[[ Run it online ]](https://utopia.sh/?code=local+better_plans+no%3B%0D%0Aunless+better_plans+%7B%0D%0A++++print+%22Conditionals+it+is%21%22%3B%0D%0A%7D%3B)
