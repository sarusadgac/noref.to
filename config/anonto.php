<?php

return [

    /*
    |--------------------------------------------------------------------------
    | System User Email
    |--------------------------------------------------------------------------
    |
    | The email address for the system account that owns guest-created links.
    |
    */

    'system_user_email' => env('ANONTO_SYSTEM_USER_EMAIL', 'system@anon.to'),

    /*
    |--------------------------------------------------------------------------
    | Link Cache TTL
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) resolved link destinations are cached.
    | Default: 24 hours (86400 seconds).
    |
    */

    'link_cache_ttl' => env('ANONTO_LINK_CACHE_TTL', 86400),

    /*
    |--------------------------------------------------------------------------
    | Redirect Log TTL
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) direct redirect log entries are retained in cache.
    | The TTL resets on every visit, so active URLs stay alive.
    | Default: 1 hour (3600 seconds).
    |
    */

    'redirect_log_ttl' => env('REDIRECT_LOG_TTL', 600),

    /*
    |--------------------------------------------------------------------------
    | Excluded Words
    |--------------------------------------------------------------------------
    |
    | Common 6-letter English words excluded from hash generation to avoid
    | generating hashes that spell out real words.
    |
    */

    'excluded_words' => [
        'abcdef', 'abroad', 'absorb', 'accept', 'access', 'across', 'acting', 'action',
        'active', 'actual', 'adding', 'adjust', 'admire', 'admits', 'adopts', 'adults',
        'advent', 'advice', 'advise', 'affair', 'affect', 'afford', 'afraid', 'agenda',
        'agents', 'agreed', 'agrees', 'aiming', 'almost', 'always', 'amount', 'amused',
        'animal', 'annual', 'answer', 'anyway', 'appear', 'appeal', 'arctic', 'argued',
        'argues', 'arisen', 'around', 'arrive', 'asking', 'aspect', 'assert', 'assess',
        'assign', 'assist', 'assume', 'assure', 'attach', 'attack', 'attain', 'attend',
        'autumn', 'avenue', 'backed', 'barely', 'basket', 'battle', 'beauty', 'became',
        'become', 'before', 'behalf', 'behind', 'beings', 'belong', 'beside', 'beyond',
        'bishop', 'blacks', 'blamed', 'blocks', 'boards', 'bodies', 'border', 'bother',
        'bottom', 'bought', 'branch', 'breath', 'bridge', 'brings', 'broken', 'brothe',
        'browse', 'budget', 'builds', 'bundle', 'burden', 'bureau', 'burned', 'butter',
        'button', 'buyers', 'buying', 'called', 'calmly', 'camera', 'cancer', 'carbon',
        'career', 'caring', 'castle', 'caught', 'caused', 'causes', 'center', 'centre',
        'chairs', 'chance', 'change', 'chapel', 'charge', 'cheese', 'choice', 'choose',
        'chosen', 'church', 'circle', 'cities', 'claims', 'closed', 'closer', 'closes',
        'clothe', 'coffee', 'column', 'combat', 'coming', 'commit', 'common', 'comply',
        'cooker', 'copied', 'corner', 'costly', 'cotton', 'counts', 'county', 'couple',
        'course', 'covers', 'create', 'credit', 'crisis', 'custom', 'cycles', 'damage',
        'danger', 'dating', 'dealer', 'debate', 'decade', 'decent', 'decide', 'deeply',
        'defeat', 'defend', 'define', 'degree', 'demand', 'denied', 'denies', 'depart',
        'depend', 'deploy', 'depths', 'deputy', 'derive', 'desert', 'design', 'desire',
        'detail', 'detect', 'device', 'devote', 'differ', 'dinner', 'direct', 'divide',
        'doctor', 'dollar', 'domain', 'donkey', 'double', 'doubts', 'dozens', 'drawer',
        'driven', 'driver', 'drives', 'during', 'earned', 'easier', 'easily', 'eating',
        'editor', 'effect', 'effort', 'eighth', 'either', 'eleven', 'emerge', 'empire',
        'employ', 'enable', 'ending', 'energy', 'engage', 'engine', 'enjoys', 'enough',
        'ensure', 'entire', 'entity', 'equals', 'escape', 'estate', 'events', 'evolve',
        'exceed', 'except', 'excite', 'excuse', 'exists', 'expand', 'expect', 'expert',
        'export', 'expose', 'extend', 'extent', 'extras', 'fabric', 'facial', 'factor',
        'failed', 'fairly', 'fallen', 'family', 'famous', 'farmer', 'father', 'favour',
        'female', 'fierce', 'figure', 'filled', 'filter', 'finale', 'finely', 'finger',
        'finish', 'firmly', 'fiscal', 'fitted', 'fixing', 'flight', 'floors', 'flower',
        'flying', 'follow', 'forced', 'forces', 'forest', 'forget', 'formal', 'format',
        'former', 'fossil', 'foster', 'fought', 'fourth', 'frames', 'freeze', 'french',
        'friend', 'frozen', 'fruits', 'fulfil', 'funded', 'fusion', 'future', 'gained',
        'galaxy', 'gamble', 'garden', 'gather', 'gender', 'gentle', 'german', 'global',
        'golden', 'govern', 'grains', 'grants', 'graves', 'greens', 'ground', 'groups',
        'growth', 'guests', 'guided', 'guides', 'guitar', 'habits', 'handle', 'happen',
        'hardly', 'having', 'headed', 'health', 'hearts', 'heaven', 'height', 'helped',
        'hidden', 'higher', 'highly', 'holder', 'horses', 'hotels', 'housed', 'houses',
        'humans', 'humble', 'hunger', 'hunter', 'ignore', 'images', 'immune', 'impact',
        'impose', 'income', 'indeed', 'inform', 'injury', 'insert', 'inside', 'insist',
        'intent', 'invest', 'invite', 'island', 'issues', 'itself', 'jacket', 'jersey',
        'joints', 'jumped', 'jungle', 'junior', 'killed', 'kindly', 'kitten', 'knight',
        'kn0wle', 'labels', 'ladder', 'landed', 'ladies', 'laptop', 'larger', 'lastly',
        'latest', 'latter', 'launch', 'layers', 'layout', 'leader', 'league', 'leaves',
        'legacy', 'lender', 'length', 'lesson', 'letter', 'levels', 'lights', 'likely',
        'limits', 'linear', 'linked', 'liquid', 'listed', 'listen', 'little', 'lively',
        'living', 'locate', 'locked', 'looked', 'losses', 'lovely', 'lovers', 'luxury',
        'mainly', 'making', 'manage', 'manner', 'manual', 'margin', 'marked', 'market',
        'master', 'matter', 'mature', 'merely', 'merger', 'method', 'middle', 'mighty',
        'minded', 'mining', 'minute', 'mirror', 'mobile', 'models', 'modern', 'modest',
        'moment', 'months', 'mother', 'motion', 'moving', 'murder', 'museum', 'mutual',
        'myself', 'namely', 'narrow', 'nation', 'nature', 'nearby', 'nearly', 'neatly',
        'needed', 'nicely', 'nights', 'nobody', 'normal', 'notice', 'noting', 'notion',
        'novels', 'number', 'nurses', 'object', 'obtain', 'occupy', 'occurs', 'offers',
        'office', 'oldest', 'online', 'opened', 'openly', 'option', 'orange', 'orders',
        'organs', 'origin', 'others', 'outfit', 'output', 'owners', 'oxygen', 'packed',
        'palace', 'panels', 'papers', 'parent', 'partly', 'passed', 'patent', 'paying',
        'paving', 'people', 'period', 'permit', 'person', 'phrase', 'picked', 'pieces',
        'placed', 'places', 'planet', 'plants', 'played', 'player', 'please', 'pledge',
        'plenty', 'pocket', 'poetry', 'points', 'poison', 'police', 'policy', 'poorly',
        'portal', 'poster', 'pounds', 'powder', 'powers', 'prayer', 'prefer', 'pretty',
        'prince', 'prison', 'profit', 'proper', 'proved', 'proven', 'proves', 'public',
        'pulled', 'purely', 'pursue', 'pushed', 'puzzle', 'queens', 'quotes', 'racial',
        'racist', 'raised', 'ranges', 'ranked', 'rarely', 'rather', 'rating', 'reader',
        'really', 'reason', 'recall', 'recent', 'record', 'reduce', 'reform', 'refuse',
        'regard', 'regime', 'region', 'relate', 'relief', 'remain', 'remind', 'remote',
        'remove', 'render', 'rental', 'repair', 'repeat', 'report', 'rescue', 'resign',
        'resist', 'resort', 'result', 'retain', 'retire', 'return', 'reveal', 'review',
        'reward', 'rights', 'rising', 'robust', 'RouteT', 'ruling', 'runner', 'rushed',
        'sacred', 'safety', 'saints', 'salary', 'sample', 'saving', 'scales', 'scheme',
        'school', 'scores', 'screen', 'search', 'season', 'second', 'secret', 'sector',
        'secure', 'seeing', 'seemed', 'select', 'seller', 'senior', 'series', 'served',
        'server', 'serves', 'settle', 'sheets', 'shield', 'shifts', 'should', 'showed',
        'signal', 'signed', 'silent', 'silver', 'simple', 'simply', 'single', 'sister',
        'skills', 'slight', 'slowly', 'smooth', 'soccer', 'social', 'solely', 'sought',
        'source', 'speech', 'spirit', 'spoken', 'spread', 'spring', 'square', 'stable',
        'stages', 'stance', 'stated', 'states', 'status', 'stayed', 'steady', 'stolen',
        'stones', 'stored', 'stores', 'strain', 'strand', 'stream', 'street', 'stress',
        'strict', 'strike', 'string', 'stroke', 'strong', 'struck', 'studio', 'submit',
        'sudden', 'suffer', 'summer', 'summit', 'supply', 'surely', 'survey', 'switch',
        'symbol', 'system', 'tables', 'tackle', 'taking', 'talent', 'target', 'taught',
        'temple', 'tenant', 'tender', 'tennis', 'thanks', 'theory', 'thirty', 'though',
        'threat', 'thrown', 'tissue', 'titles', 'toward', 'towers', 'traces', 'tracks',
        'trades', 'travel', 'treats', 'trends', 'tricks', 'troops', 'truths', 'trying',
        'tunnel', 'turned', 'twelve', 'twenty', 'typing', 'unable', 'unions', 'unique',
        'united', 'unless', 'unlike', 'update', 'useful', 'valley', 'valued', 'values',
        'varied', 'varies', 'verbal', 'versus', 'victim', 'Vienna', 'vision', 'visits',
        'voices', 'volume', 'voters', 'voyage', 'waited', 'waiter', 'walked', 'wanted',
        'warmth', 'warned', 'waters', 'wealth', 'weapon', 'weekly', 'weight', 'widely',
        'window', 'winner', 'winter', 'wisdom', 'wished', 'wishes', 'within', 'wonder',
        'wooden', 'worker', 'worthy', 'wounds', 'writer', 'writes', 'yellow', 'youths',
    ],

];
