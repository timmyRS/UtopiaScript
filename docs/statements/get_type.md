# get_type

- Aliases: `gettype`, `type_of`, `typeof`
- Syntax: `get_type <whatever>`

Returns the type of whatever it been provided with.

This includes [variable types](../types):

    print_line get_type "Hi"; # string
    print_line get_type 69; # number
    print_line get_type true; # boolean
    print_line get_type null; # null

[[ Run it online ]](https://utopia.sh/?code=print_line+get_type+%22Hi%22%3B+%23+string%0D%0Aprint_line+get_type+69%3B+%23+number%0D%0Aprint_line+get_type+true%3B+%23+boolean%0D%0Aprint_line+get_type+null%3B+%23+null)

but also "statement" and "undefined" if the given argument is unknown as a statement or variable:

    print_line get_type print; # statement
    print_line get_type bla; # undefined

[[ Run it online ]](https://utopia.sh/?code=print_line+get_type+print%3B+%23+statement%0D%0Aprint_line+get_type+bla%3B+%23+undefined)
