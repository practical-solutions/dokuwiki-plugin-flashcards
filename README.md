# Flashcards-Plugin for DokuWiki

Converts some content into a set of scrollable/playable flashcards. Does NOT support the Internet Explorer!

## Usage

Enclose the flashcards with ``<cards>...</cards>``. The cards are separated with a horizontal line ``----``.

Example:

```
<cards>
First card
----
Second card
----
Third card
</cards>
```

## Card Counter

Placing ``~~countcards~~`` on  a page will count all the cards in the namespace recursively.

The results are cached and will only be updated on "purge" of a page"


## New Features

* Scroll-Top-Setting
* Card counter
* List media files

## Comptability

Tested with
* PHP **7.3**
* DokuWiki / **Greebo** (NOT compatbile with Hogfather yet!)
