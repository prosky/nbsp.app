
export default {
    non_breaking_hyphen: [
        '@(\\w{1})-(\\w+)@i',
        '$1‑$2'
    ],
    numbers: [
        '@(\\d) (\\d)@i',
        '$1 $2'
    ],
    spaces_in_scales: [
        '@(\\d) : (\\d)@i',
        '$1 : $2'
    ],
    ordered_number: [
        '@(\\d\\.) ([0-9a-záčďéěíňóřšťúýž])@i',
        '$1 $2'
    ],
    abbreviations: [
        '@(^|$|;| |&nbsp;|\\(|\\n|>)(vč.|cca.|č.|čís.|čj.|čp.|fa|fě|fy|kupř.|mj.|např.|p.|pí|popř.|př.|přib.|přibl.|sl.|str.|sv.|tj.|tzn.|tzv.|zvl.) @i',
        '$1$2 '
    ],
    prepositions: [
        '@(^|$|;| |&nbsp;|\\(|\\n|>)(do|kromě|od|u|z|ze|za|proti|naproti|kvůli|vůči|nad|pod|před|za|o|pro|mezi|přes|mimo|při|na|po|v|ve|pod|před|s|za|mezi|se|si|k|je) @i',
        '$1$2 '
    ],
    conjunctions: [
        '@(^|$|;| |&nbsp;|\\(|\\n|>)(a|i|o|u) @i',
        '$1$2 '
    ],
    units: [
        '@(\\d) (m|m²|l|kg|h|°C|Kč|lidí|dní|%|mil)(^|[;\\.!:]| | |\\?|\\n|\\)|<|\\010|\\013|$)@i',
        '$1 $2$3'
    ],
    short_words: [
        '@(^|$|;| |&nbsp;|\\(|\\n|>)(.{1,3}) @i',
        '$1$2 '
    ]
};

