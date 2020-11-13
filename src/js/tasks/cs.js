import {space} from "../const";

const prepositions = 'do|kromě|od|u|z|ze|za|proti|naproti|kvůli|vůči|nad|pod|před|za|o|pro|mezi|přes|mimo|při|na|po|v|ve|pod|před|s|za|mezi|se|si|k|je';
const abbreviations = 'vč.|cca.|č.|čís.|čj.|čp.|fa|fě|fy|kupř.|mj.|např.|p.|pí|popř.|př.|přib.|přibl.|sl.|str.|sv.|tj.|tzn.|tzv.|zvl.';
const units = 'm|m²|l|kg|h|°C|Kč|lidí|dní|%|mil';
const allChars = '[0-9a-záčďéěíňóřšťúýž]';
const conjunctions = 'a|i|o|u';

export default {
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
    abbreviations: [
        `@(${space})(${abbreviations}) @i`,
        `$1$2 `
    ],
    prepositions: [
        `@(${space})(${prepositions}) @i`,
        `$1$2 `
    ],
    conjunctions: [
        `@(${space})(${conjunctions}) @i`,
        `$1$2 `
    ],
    units: [
        `@(\\d) (${units})(^|[;\\.!:]| | |\\?|\\n|\\)|<|\\010|\\013|$)@i`,
        `$1 $2$3`
    ],
    short_words: [
        `@(${space})(.{1,3}) @i`,
        `$1$2 `
    ]
};

