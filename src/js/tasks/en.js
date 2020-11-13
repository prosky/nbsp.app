import {space} from "../const";

const prepositions = 'aboard|about|above|across|after|against|ahead of|along|amid|amidst|among|around|are|as|as far as|as of|aside from|at|athwart|atop|be|barring|because of|before|behind|below|beneath|beside|besides|between|beyond|but|by|by means of|circa|concerning|despite|down|during|except|except for|excluding|far from|following|for|from|is|in|in accordance with|in addition to|in case of|in front of|in lieu of|in place of|in spite of|including|inside|instead of|into|like|minus|near|next to|notwithstanding|of|off|on|on account of|on behalf of|on top of|onto|opposite|out|out of|outside|over|past|plus|prior to|regarding|regardless of|save|since|than|through|throughout|till|to|toward|towards|under|underneath|unlike|until|up|upon|versus|via|with|with regard to|within|without';
const units = 'm|m²|l|kg|h|°C|Kč|peoples|days|moths|%|miles';
const allChars = '[0-9a-z]';
const conjunctions = 'and|at|even|about|or|to';
const article = 'a|an|the';

export default   {
    short_words: [
        `@(${space})(.{1,3}) @i`,
        `$1$2 `
    ],
        non_breaking_hyphen: [
        `@(\\w{1})-(\\w+)@i`,
        `$1‑$2`
    ],
        numbers: [
        `@(\\d) (\\d)@i`,
        `$1 $2`
    ],
        spaces_in_scales: [
        `@(\\d) : (\\d)@i`,
        `$1 : $2`
    ],
        ordered_number: [
        `@(\\d\\.) (${allChars})@i`,
        `$1 $2`
    ],
        prepositions: [
        `@(${space})(${prepositions}) @i`,
        `$1$2 `
    ],
        conjunctions: [
        `@(${space})(${conjunctions}) @i`,
        `$1$2 `
    ],
        article: [
        `@(${space})(${article}) @i`,
        `$1$2 `
    ],
        units: [
        `@(\\d) (${units})(^|[;\\.!:]| | |\\?|\\n|\\)|<|\\010|\\013|$)@i`,
        `$1 $2$3`
    ]
};

