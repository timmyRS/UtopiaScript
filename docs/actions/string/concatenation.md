# Concatenation

- Syntax: `<string|number> <string|number>` 

Turns two or more variables into a single string: 

    print "That's " 20 "%!"; # That's 20%!

[[ Run it online ]](https://utopia.sh/?code=print+%22That%27s+%22+20+%22%25%21%22%3B+%23+That%27s+20%25%21)

Numbers can not only be used as arguments for concatenation, but also to initiate it:

    print 5 " more days"; # 5 more days

[[ Run it online ]](https://utopia.sh/?code=print+5+%22+more+days%22%3B+%23+5+more+days)

Note that numbers will have to be immediately followed by a string, unlike strings which take anything in any amount:

    print_line "I have " 1 0 " euros!"; # I have 10 euros!
    print_line 1 0 " euros I have!"; # produces an error

[[ Run it online ]](https://utopia.sh/?code=print_line+%22I+have+%22+1+0+%22+euros%21%22%3B+%23+I+have+10+euros%21%0D%0Aprint_line+1+0+%22+euros+I+have%21%22%3B+%23+produces+an+error)
