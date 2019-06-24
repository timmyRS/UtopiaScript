# Language Constructs

- [Comments](#comments)
- [Parentheses](#parentheses)

## Comments

You can comment your code using `#` or `//` so you won't forget what it does:

    final greeting "Hello"; // Define the greeting
    print greeting ", world!"; # Greet the world using the greeting we just defined

If you like comments that go for multiple lines and/or ASCII art, feel free to use `/*` and `*/`:

    /**** file.us
     * Copyright (c) 20XX, John Doe
     * Licensed under the MIT license
     *********************************/

## Parentheses

Wrapping a statement in parentheses will execute it and return its response, which can be used, e.g., to avoid having to create temporary variables:

    final cph 20;
    final hours 8;
    final total = cph * hours;
    print "I can bake "cph" cookies per hour, meaning I could bake "total" in "hours" hours";

[[ Run it online ]](https://utopia.sh/?code=print+%22I+can+bake+20+cookies+per+hour%2C+meaning+I+could+bake+%22+%2820+*+8%29+%22+in+8+hours%22%3B)

This can be done without the `total` variable without sacrificing readability:

    final cph 20;
    final hours 8;
    print "I can bake "cph" cookies per hour, meaning I could bake "(cph * hours)" in "hours" hours";

[[ Run it online ]](https://utopia.sh/?code=final+cph+20%3B%0D%0Afinal+hours+8%3B%0D%0Aprint+%22I+can+bake+%22cph%22+cookies+per+hour%2C+meaning+I+could+bake+%22%28cph+*+hours%29%22+in+%22hours%22+hours%22%3B)
