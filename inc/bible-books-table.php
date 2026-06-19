<?php
/**
 * Interactive Bible books periodic table.
 *
 * @package brendon-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BB_BIBLE_BOOKS_USER_META', 'bb_bible_books_completed' );

function brendon_core_bible_books_table_books() {
	$books = array(
		array( 'slug' => 'genesis', 'abbr' => 'Gn', 'title' => 'Genesis', 'testament' => 'Old Testament', 'group' => 'Torah', 'chapters' => 50, 'col' => 1, 'row' => 1, 'summary' => 'Beginnings, covenant, creation, fall, flood, and the family of Abraham.' ),
		array( 'slug' => 'exodus', 'abbr' => 'Ex', 'title' => 'Exodus', 'testament' => 'Old Testament', 'group' => 'Torah', 'chapters' => 40, 'col' => 1, 'row' => 2, 'summary' => 'Deliverance from Egypt, covenant at Sinai, and the tabernacle.' ),
		array( 'slug' => 'leviticus', 'abbr' => 'Lv', 'title' => 'Leviticus', 'testament' => 'Old Testament', 'group' => 'Torah', 'chapters' => 27, 'col' => 1, 'row' => 3, 'summary' => 'Holiness, worship, sacrifice, and life with God at the center.' ),
		array( 'slug' => 'numbers', 'abbr' => 'Nu', 'title' => 'Numbers', 'testament' => 'Old Testament', 'group' => 'Torah', 'chapters' => 36, 'col' => 1, 'row' => 4, 'summary' => 'Wilderness wandering, rebellion, mercy, and preparation for the land.' ),
		array( 'slug' => 'deuteronomy', 'abbr' => 'Dt', 'title' => 'Deuteronomy', 'testament' => 'Old Testament', 'group' => 'Torah', 'chapters' => 34, 'col' => 1, 'row' => 5, 'summary' => 'Moses renews the covenant and calls Israel to faithful love.' ),
		array( 'slug' => 'joshua', 'abbr' => 'Js', 'title' => 'Joshua', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 24, 'col' => 2, 'row' => 1, 'summary' => 'Israel enters the land and is called to courage and covenant faithfulness.' ),
		array( 'slug' => 'judges', 'abbr' => 'Ju', 'title' => 'Judges', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 21, 'col' => 2, 'row' => 2, 'summary' => 'Cycles of rebellion, rescue, and the need for righteous leadership.' ),
		array( 'slug' => 'ruth', 'abbr' => 'Ru', 'title' => 'Ruth', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 4, 'col' => 2, 'row' => 3, 'summary' => 'Loyal love, redemption, and God working through ordinary faithfulness.' ),
		array( 'slug' => '1-samuel', 'abbr' => 'Sa', 'title' => '1 Samuel', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 31, 'col' => 2, 'row' => 4, 'summary' => 'Samuel, Saul, David, and the birth pains of Israel\'s monarchy.' ),
		array( 'slug' => '2-samuel', 'abbr' => 'Sa', 'title' => '2 Samuel', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 24, 'col' => 2, 'row' => 5, 'summary' => 'David\'s reign, covenant promise, failure, repentance, and consequence.' ),
		array( 'slug' => '1-kings', 'abbr' => 'Ki', 'title' => '1 Kings', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 22, 'col' => 2, 'row' => 6, 'summary' => 'Solomon, the temple, the divided kingdom, and prophetic confrontation.' ),
		array( 'slug' => '2-kings', 'abbr' => 'Ki', 'title' => '2 Kings', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 25, 'col' => 2, 'row' => 7, 'summary' => 'Kings rise and fall as exile arrives after persistent covenant rebellion.' ),
		array( 'slug' => '1-chronicles', 'abbr' => 'Ch', 'title' => '1 Chronicles', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 29, 'col' => 3, 'row' => 1, 'summary' => 'Genealogies and David\'s kingdom retold with worship and temple focus.' ),
		array( 'slug' => '2-chronicles', 'abbr' => 'Ch', 'title' => '2 Chronicles', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 36, 'col' => 3, 'row' => 2, 'summary' => 'Judah\'s kings, temple worship, exile, and hope for restoration.' ),
		array( 'slug' => 'ezra', 'abbr' => 'Ez', 'title' => 'Ezra', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 10, 'col' => 3, 'row' => 3, 'summary' => 'Return from exile, rebuilding worship, and renewal through the Word.' ),
		array( 'slug' => 'nehemiah', 'abbr' => 'Ne', 'title' => 'Nehemiah', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 13, 'col' => 3, 'row' => 4, 'summary' => 'Walls rebuilt, people gathered, and reform pursued with prayerful grit.' ),
		array( 'slug' => 'esther', 'abbr' => 'Es', 'title' => 'Esther', 'testament' => 'Old Testament', 'group' => 'History', 'chapters' => 10, 'col' => 3, 'row' => 5, 'summary' => 'Providence, courage, and deliverance in a foreign empire.' ),
		array( 'slug' => 'job', 'abbr' => 'Jb', 'title' => 'Job', 'testament' => 'Old Testament', 'group' => 'Poetry', 'chapters' => 42, 'col' => 4, 'row' => 1, 'summary' => 'Suffering, wisdom, lament, and trusting God beyond easy answers.' ),
		array( 'slug' => 'psalms', 'abbr' => 'Ps', 'title' => 'Psalms', 'testament' => 'Old Testament', 'group' => 'Poetry', 'chapters' => 150, 'col' => 4, 'row' => 2, 'summary' => 'Prayer, praise, lament, wisdom, kingship, and worship for every season.' ),
		array( 'slug' => 'proverbs', 'abbr' => 'Pr', 'title' => 'Proverbs', 'testament' => 'Old Testament', 'group' => 'Poetry', 'chapters' => 31, 'col' => 4, 'row' => 3, 'summary' => 'Wisdom for daily life rooted in the fear of the Lord.' ),
		array( 'slug' => 'ecclesiastes', 'abbr' => 'Ec', 'title' => 'Ecclesiastes', 'testament' => 'Old Testament', 'group' => 'Poetry', 'chapters' => 12, 'col' => 4, 'row' => 4, 'summary' => 'Life\'s vapor, honest questions, and reverence before God.' ),
		array( 'slug' => 'song-of-solomon', 'abbr' => 'So', 'title' => 'Song of Solomon', 'testament' => 'Old Testament', 'group' => 'Poetry', 'chapters' => 8, 'col' => 4, 'row' => 5, 'summary' => 'Love, longing, beauty, and covenant delight.' ),
		array( 'slug' => 'isaiah', 'abbr' => 'Is', 'title' => 'Isaiah', 'testament' => 'Old Testament', 'group' => 'Major Prophets', 'chapters' => 66, 'col' => 5, 'row' => 1, 'summary' => 'Judgment, comfort, holiness, servant hope, and new creation promise.' ),
		array( 'slug' => 'jeremiah', 'abbr' => 'Je', 'title' => 'Jeremiah', 'testament' => 'Old Testament', 'group' => 'Major Prophets', 'chapters' => 52, 'col' => 5, 'row' => 2, 'summary' => 'A prophet weeps as judgment falls and a new covenant is promised.' ),
		array( 'slug' => 'lamentations', 'abbr' => 'La', 'title' => 'Lamentations', 'testament' => 'Old Testament', 'group' => 'Major Prophets', 'chapters' => 5, 'col' => 5, 'row' => 3, 'summary' => 'Grief over Jerusalem\'s fall and hope in God\'s steadfast mercy.' ),
		array( 'slug' => 'ezekiel', 'abbr' => 'Ek', 'title' => 'Ezekiel', 'testament' => 'Old Testament', 'group' => 'Major Prophets', 'chapters' => 48, 'col' => 5, 'row' => 4, 'summary' => 'Glory, exile, judgment, restoration, and a heart made new.' ),
		array( 'slug' => 'daniel', 'abbr' => 'Da', 'title' => 'Daniel', 'testament' => 'Old Testament', 'group' => 'Major Prophets', 'chapters' => 12, 'col' => 5, 'row' => 5, 'summary' => 'Faithfulness in exile and visions of God\'s lasting kingdom.' ),
		array( 'slug' => 'hosea', 'abbr' => 'Ho', 'title' => 'Hosea', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 14, 'col' => 6, 'row' => 1, 'summary' => 'Covenant love pictured through heartbreak, betrayal, and mercy.' ),
		array( 'slug' => 'joel', 'abbr' => 'Jl', 'title' => 'Joel', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 3, 'col' => 6, 'row' => 2, 'summary' => 'The day of the Lord, repentance, restoration, and poured-out Spirit.' ),
		array( 'slug' => 'amos', 'abbr' => 'Am', 'title' => 'Amos', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 9, 'col' => 6, 'row' => 3, 'summary' => 'Justice, worship integrity, and warning against hollow religion.' ),
		array( 'slug' => 'obadiah', 'abbr' => 'Ob', 'title' => 'Obadiah', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 1, 'col' => 6, 'row' => 4, 'summary' => 'Pride judged and the Lord\'s kingdom declared.' ),
		array( 'slug' => 'jonah', 'abbr' => 'Jh', 'title' => 'Jonah', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 4, 'col' => 6, 'row' => 5, 'summary' => 'Mercy stretches wider than Jonah wants and farther than enemies expect.' ),
		array( 'slug' => 'micah', 'abbr' => 'Mi', 'title' => 'Micah', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 7, 'col' => 7, 'row' => 1, 'summary' => 'Justice, mercy, humility, judgment, and Bethlehem hope.' ),
		array( 'slug' => 'nahum', 'abbr' => 'Na', 'title' => 'Nahum', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 3, 'col' => 7, 'row' => 2, 'summary' => 'Nineveh\'s downfall and the Lord\'s justice over violent power.' ),
		array( 'slug' => 'habakkuk', 'abbr' => 'Hk', 'title' => 'Habakkuk', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 3, 'col' => 7, 'row' => 3, 'summary' => 'Questions, waiting, and living by faith when judgment is confusing.' ),
		array( 'slug' => 'zephaniah', 'abbr' => 'Zp', 'title' => 'Zephaniah', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 3, 'col' => 7, 'row' => 4, 'summary' => 'The day of the Lord, purification, and God rejoicing over his people.' ),
		array( 'slug' => 'haggai', 'abbr' => 'Ha', 'title' => 'Haggai', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 2, 'col' => 7, 'row' => 5, 'summary' => 'A call to rebuild the temple and reorder devotion.' ),
		array( 'slug' => 'zechariah', 'abbr' => 'Zc', 'title' => 'Zechariah', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 14, 'col' => 7, 'row' => 6, 'summary' => 'Visions of restoration, a coming king, and renewed Jerusalem.' ),
		array( 'slug' => 'malachi', 'abbr' => 'Ml', 'title' => 'Malachi', 'testament' => 'Old Testament', 'group' => 'Minor Prophets', 'chapters' => 4, 'col' => 7, 'row' => 7, 'summary' => 'Covenant correction and expectation before the Lord comes.' ),
		array( 'slug' => 'matthew', 'abbr' => 'Mt', 'title' => 'Matthew', 'testament' => 'New Testament', 'group' => 'Gospels', 'chapters' => 28, 'col' => 9, 'row' => 1, 'summary' => 'Jesus the Messiah, teacher, king, and fulfillment of Scripture.' ),
		array( 'slug' => 'mark', 'abbr' => 'Mk', 'title' => 'Mark', 'testament' => 'New Testament', 'group' => 'Gospels', 'chapters' => 16, 'col' => 9, 'row' => 2, 'summary' => 'Jesus the suffering Son of God moving with urgent authority.' ),
		array( 'slug' => 'luke', 'abbr' => 'Lk', 'title' => 'Luke', 'testament' => 'New Testament', 'group' => 'Gospels', 'chapters' => 24, 'col' => 9, 'row' => 3, 'summary' => 'Jesus brings salvation for outsiders, the poor, and the whole world.' ),
		array( 'slug' => 'john', 'abbr' => 'Jn', 'title' => 'John', 'testament' => 'New Testament', 'group' => 'Gospels', 'chapters' => 21, 'col' => 9, 'row' => 4, 'summary' => 'Signs, glory, belief, and life in the Son of God.' ),
		array( 'slug' => 'acts', 'abbr' => 'Ac', 'title' => 'Acts', 'testament' => 'New Testament', 'group' => 'Acts', 'chapters' => 28, 'col' => 9, 'row' => 6, 'summary' => 'The Spirit sends the church from Jerusalem to the nations.' ),
		array( 'slug' => 'romans', 'abbr' => 'Ro', 'title' => 'Romans', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 16, 'col' => 10, 'row' => 1, 'summary' => 'The gospel of righteousness, grace, faith, Israel, and transformed life.' ),
		array( 'slug' => '1-corinthians', 'abbr' => 'Co', 'title' => '1 Corinthians', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 16, 'col' => 10, 'row' => 2, 'summary' => 'A divided church corrected by the cross, love, and resurrection hope.' ),
		array( 'slug' => '2-corinthians', 'abbr' => 'Co', 'title' => '2 Corinthians', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 13, 'col' => 10, 'row' => 3, 'summary' => 'Weakness, comfort, generosity, and ministry shaped by Christ.' ),
		array( 'slug' => 'galatians', 'abbr' => 'Ga', 'title' => 'Galatians', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 6, 'col' => 10, 'row' => 4, 'summary' => 'Freedom in Christ, justification by faith, and life in the Spirit.' ),
		array( 'slug' => 'ephesians', 'abbr' => 'Ep', 'title' => 'Ephesians', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 6, 'col' => 10, 'row' => 5, 'summary' => 'Union with Christ, one new humanity, and mature church life.' ),
		array( 'slug' => 'philippians', 'abbr' => 'Pp', 'title' => 'Philippians', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 4, 'col' => 10, 'row' => 6, 'summary' => 'Joy, humility, perseverance, and Christ as surpassing treasure.' ),
		array( 'slug' => 'colossians', 'abbr' => 'Cl', 'title' => 'Colossians', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 4, 'col' => 11, 'row' => 1, 'summary' => 'Christ supreme over creation, redemption, wisdom, and daily life.' ),
		array( 'slug' => '1-thessalonians', 'abbr' => 'Th', 'title' => '1 Thessalonians', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 5, 'col' => 11, 'row' => 2, 'summary' => 'Faithful endurance, holy living, encouragement, and Christ\'s return.' ),
		array( 'slug' => '2-thessalonians', 'abbr' => 'Th', 'title' => '2 Thessalonians', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 3, 'col' => 11, 'row' => 3, 'summary' => 'Steadfastness, final justice, and faithful work while waiting.' ),
		array( 'slug' => '1-timothy', 'abbr' => 'Ti', 'title' => '1 Timothy', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 6, 'col' => 11, 'row' => 4, 'summary' => 'Church order, sound teaching, leadership, and godliness.' ),
		array( 'slug' => '2-timothy', 'abbr' => 'Ti', 'title' => '2 Timothy', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 4, 'col' => 11, 'row' => 5, 'summary' => 'Endurance, Scripture, courage, and finishing the race.' ),
		array( 'slug' => 'titus', 'abbr' => 'Tt', 'title' => 'Titus', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 3, 'col' => 11, 'row' => 6, 'summary' => 'Sound doctrine, good works, and ordered church life in Crete.' ),
		array( 'slug' => 'philemon', 'abbr' => 'Pm', 'title' => 'Philemon', 'testament' => 'New Testament', 'group' => "Paul's Letters", 'chapters' => 1, 'col' => 11, 'row' => 7, 'summary' => 'A personal appeal for reconciliation shaped by the gospel.' ),
		array( 'slug' => 'hebrews', 'abbr' => 'Hb', 'title' => 'Hebrews', 'testament' => 'New Testament', 'group' => 'General Letters', 'chapters' => 13, 'col' => 12, 'row' => 1, 'summary' => 'Jesus is better: priest, sacrifice, mediator, and final word.' ),
		array( 'slug' => 'james', 'abbr' => 'Ja', 'title' => 'James', 'testament' => 'New Testament', 'group' => 'General Letters', 'chapters' => 5, 'col' => 12, 'row' => 2, 'summary' => 'Practical wisdom where faith becomes endurance, speech, mercy, and action.' ),
		array( 'slug' => '1-peter', 'abbr' => 'Pe', 'title' => '1 Peter', 'testament' => 'New Testament', 'group' => 'General Letters', 'chapters' => 5, 'col' => 12, 'row' => 3, 'summary' => 'Hope, holiness, and witness for exiles under pressure.' ),
		array( 'slug' => '2-peter', 'abbr' => 'Pe', 'title' => '2 Peter', 'testament' => 'New Testament', 'group' => 'General Letters', 'chapters' => 3, 'col' => 12, 'row' => 4, 'summary' => 'Growth, true teaching, judgment, and the promise of Christ\'s return.' ),
		array( 'slug' => '1-john', 'abbr' => 'Jn', 'title' => '1 John', 'testament' => 'New Testament', 'group' => 'General Letters', 'chapters' => 5, 'col' => 12, 'row' => 5, 'summary' => 'Assurance through light, love, truth, and confession of Christ.' ),
		array( 'slug' => '2-john', 'abbr' => 'Jn', 'title' => '2 John', 'testament' => 'New Testament', 'group' => 'General Letters', 'chapters' => 1, 'col' => 12, 'row' => 6, 'summary' => 'Walking in truth and love while guarding the church.' ),
		array( 'slug' => '3-john', 'abbr' => 'Jn', 'title' => '3 John', 'testament' => 'New Testament', 'group' => 'General Letters', 'chapters' => 1, 'col' => 12, 'row' => 7, 'summary' => 'Hospitality, faithful support, and leadership tested by character.' ),
		array( 'slug' => 'jude', 'abbr' => 'Ju', 'title' => 'Jude', 'testament' => 'New Testament', 'group' => 'General Letters', 'chapters' => 1, 'col' => 12, 'row' => 8, 'summary' => 'Contending for the faith while resting in God\'s keeping power.' ),
		array( 'slug' => 'revelation', 'abbr' => 'Re', 'title' => 'Revelation', 'testament' => 'New Testament', 'group' => 'Prophecy', 'chapters' => 22, 'col' => 13, 'row' => 8, 'summary' => 'Apocalypse, worship, endurance, judgment, and all things made new.' ),
	);

	$layout = array(
		'genesis'           => array( 1, 2 ),
		'exodus'            => array( 2, 2 ),
		'leviticus'         => array( 3, 2 ),
		'numbers'           => array( 4, 2 ),
		'deuteronomy'       => array( 5, 2 ),
		'joshua'            => array( 1, 4 ),
		'judges'            => array( 2, 4 ),
		'ruth'              => array( 3, 4 ),
		'1-samuel'          => array( 4, 4 ),
		'2-samuel'          => array( 5, 4 ),
		'1-kings'           => array( 6, 4 ),
		'2-kings'           => array( 7, 4 ),
		'1-chronicles'      => array( 8, 4 ),
		'2-chronicles'      => array( 9, 4 ),
		'ezra'              => array( 10, 4 ),
		'nehemiah'          => array( 11, 4 ),
		'esther'            => array( 12, 4 ),
		'job'               => array( 1, 6 ),
		'psalms'            => array( 2, 6 ),
		'proverbs'          => array( 3, 6 ),
		'ecclesiastes'      => array( 4, 6 ),
		'song-of-solomon'   => array( 5, 6 ),
		'isaiah'            => array( 1, 8 ),
		'jeremiah'          => array( 2, 8 ),
		'lamentations'      => array( 3, 8 ),
		'ezekiel'           => array( 4, 8 ),
		'daniel'            => array( 5, 8 ),
		'hosea'             => array( 1, 10 ),
		'joel'              => array( 2, 10 ),
		'amos'              => array( 3, 10 ),
		'obadiah'           => array( 4, 10 ),
		'jonah'             => array( 5, 10 ),
		'micah'             => array( 6, 10 ),
		'nahum'             => array( 7, 10 ),
		'habakkuk'          => array( 8, 10 ),
		'zephaniah'         => array( 9, 10 ),
		'haggai'            => array( 10, 10 ),
		'zechariah'         => array( 11, 10 ),
		'malachi'           => array( 12, 10 ),
		'matthew'           => array( 1, 12 ),
		'mark'              => array( 2, 12 ),
		'luke'              => array( 3, 12 ),
		'john'              => array( 4, 12 ),
		'acts'              => array( 6, 12 ),
		'romans'            => array( 1, 14 ),
		'1-corinthians'     => array( 2, 14 ),
		'2-corinthians'     => array( 3, 14 ),
		'galatians'         => array( 4, 14 ),
		'ephesians'         => array( 5, 14 ),
		'philippians'       => array( 6, 14 ),
		'colossians'        => array( 7, 14 ),
		'1-thessalonians'   => array( 8, 14 ),
		'2-thessalonians'   => array( 9, 14 ),
		'1-timothy'         => array( 10, 14 ),
		'2-timothy'         => array( 11, 14 ),
		'titus'             => array( 12, 14 ),
		'philemon'          => array( 13, 14 ),
		'hebrews'           => array( 1, 16 ),
		'james'             => array( 2, 16 ),
		'1-peter'           => array( 3, 16 ),
		'2-peter'           => array( 4, 16 ),
		'1-john'            => array( 5, 16 ),
		'2-john'            => array( 6, 16 ),
		'3-john'            => array( 7, 16 ),
		'jude'              => array( 8, 16 ),
		'revelation'        => array( 13, 16 ),
	);

	foreach ( $books as &$book ) {
		if ( isset( $layout[ $book['slug'] ] ) ) {
			$book['col'] = $layout[ $book['slug'] ][0];
			$book['row'] = $layout[ $book['slug'] ][1];
		}
	}
	unset( $book );

	return apply_filters( 'brendon_core_bible_books_table_books', $books );
}

function brendon_core_bible_books_table_completed( $user_id = 0 ) {
	$user_id   = $user_id ? (int) $user_id : get_current_user_id();
	$completed = $user_id ? get_user_meta( $user_id, BB_BIBLE_BOOKS_USER_META, true ) : array();

	return is_array( $completed ) ? array_values( array_unique( array_map( 'sanitize_key', $completed ) ) ) : array();
}

function brendon_core_bible_books_table_login_url( $redirect_url = '' ) {
	$page = get_page_by_path( 'login' );
	$url  = $page ? get_permalink( $page ) : wp_login_url( $redirect_url );

	return $redirect_url && $page ? add_query_arg( 'redirect_to', rawurlencode( $redirect_url ), $url ) : $url;
}

function brendon_core_bible_books_table_page_url() {
	$page = get_page_by_path( 'bible-books' );

	return $page ? get_permalink( $page ) : home_url( '/bible-books/' );
}

function brendon_core_bible_books_table_profile_user_id() {
	if ( function_exists( 'um_profile_id' ) ) {
		$user_id = absint( um_profile_id() );
		if ( $user_id ) {
			return $user_id;
		}
	}

	$query_user = get_query_var( 'um_user' );
	if ( $query_user ) {
		$user = is_numeric( $query_user ) ? get_userdata( absint( $query_user ) ) : get_user_by( 'login', sanitize_user( $query_user ) );
		if ( $user ) {
			return (int) $user->ID;
		}
	}

	return get_current_user_id();
}

function brendon_core_bible_books_table_profile_card() {
	static $rendered = false;

	if ( $rendered ) {
		return;
	}

	$user_id = brendon_core_bible_books_table_profile_user_id();
	if ( ! $user_id ) {
		return;
	}

	$rendered = true;

	$books        = brendon_core_bible_books_table_books();
	$completed    = brendon_core_bible_books_table_completed( $user_id );
	$total_books  = count( $books );
	$done_count   = count( $completed );
	$progress     = $total_books ? round( ( $done_count / $total_books ) * 100 ) : 0;
	$recent_books = array_slice(
		array_values(
			array_filter(
				$books,
				function ( $book ) use ( $completed ) {
					return in_array( $book['slug'], $completed, true );
				}
			)
		),
		-5
	);
	?>
	<section class="bb-um-bible-progress">
		<div class="bb-um-bible-progress__header">
			<div>
				<p class="bb-kicker"><?php esc_html_e( 'Bible Books', 'brendon-core' ); ?></p>
				<h3><?php esc_html_e( 'Reading progress', 'brendon-core' ); ?></h3>
			</div>
			<strong><?php echo esc_html( $progress ); ?>%</strong>
		</div>

		<div class="bb-um-bible-progress__meter" aria-hidden="true">
			<span style="width: <?php echo esc_attr( $progress ); ?>%;"></span>
		</div>

		<p>
			<?php
			printf(
				/* translators: 1: completed count, 2: total count. */
				esc_html__( '%1$s of %2$s books completed.', 'brendon-core' ),
				esc_html( number_format_i18n( $done_count ) ),
				esc_html( number_format_i18n( $total_books ) )
			);
			?>
		</p>

		<?php if ( $recent_books ) : ?>
			<div class="bb-um-bible-progress__books" aria-label="<?php echo esc_attr_x( 'Recently completed Bible books', 'aria label', 'brendon-core' ); ?>">
				<?php foreach ( $recent_books as $book ) : ?>
					<span><?php echo esc_html( $book['abbr'] ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<a class="bb-um-bible-progress__link" href="<?php echo esc_url( brendon_core_bible_books_table_page_url() ); ?>">
			<?php esc_html_e( 'Open Bible table', 'brendon-core' ); ?>
		</a>
	</section>
	<?php
}

function brendon_core_bible_books_table_profile_tab( $tabs ) {
	$tabs['bible_books'] = array(
		'name'            => __( 'Bible Progress', 'brendon-core' ),
		'icon'            => 'um-faicon-book',
		'custom'          => true,
		'default_privacy' => 0,
	);

	return $tabs;
}
add_filter( 'um_profile_tabs', 'brendon_core_bible_books_table_profile_tab', 20 );
add_filter( 'um_user_profile_tabs', 'brendon_core_bible_books_table_profile_tab', 20 );
add_action( 'um_profile_content_bible_books', 'brendon_core_bible_books_table_profile_card', 20 );
add_action( 'um_profile_content_bible_books_default', 'brendon_core_bible_books_table_profile_card', 20 );

function brendon_core_bible_books_table_ajax_toggle() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Please log in to save Bible book progress.', 'brendon-core' ) ), 401 );
	}

	check_ajax_referer( 'bb_bible_books_table', 'nonce' );

	$slug  = isset( $_POST['book'] ) ? sanitize_key( wp_unslash( $_POST['book'] ) ) : '';
	$books = brendon_core_bible_books_table_books();
	$valid = wp_list_pluck( $books, 'slug' );

	if ( ! $slug || ! in_array( $slug, $valid, true ) ) {
		wp_send_json_error( array( 'message' => __( 'Bible book not found.', 'brendon-core' ) ), 404 );
	}

	$user_id   = get_current_user_id();
	$completed = brendon_core_bible_books_table_completed( $user_id );
	$is_done   = in_array( $slug, $completed, true );

	if ( $is_done ) {
		$completed = array_values( array_diff( $completed, array( $slug ) ) );
	} else {
		$completed[] = $slug;
		$completed   = array_values( array_unique( $completed ) );
	}

	update_user_meta( $user_id, BB_BIBLE_BOOKS_USER_META, $completed );

	wp_send_json_success(
		array(
			'book'      => $slug,
			'completed' => ! $is_done,
			'count'     => count( $completed ),
			'total'     => count( $books ),
		)
	);
}
add_action( 'wp_ajax_bb_bible_books_table_toggle', 'brendon_core_bible_books_table_ajax_toggle' );

function brendon_core_bible_books_table_assets() {
	if ( ! is_page( 'bible-books' ) && ! is_page_template( 'page-bible-books.php' ) ) {
		return;
	}

	$css_path = get_template_directory() . '/assets/css/bible-books-table.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'brendon-core-bible-books-table',
			get_template_directory_uri() . '/assets/css/bible-books-table.css',
			array( 'brendon-core-brand-theme' ),
			filemtime( $css_path )
		);
	}

	$js_path = get_template_directory() . '/assets/js/bible-books-table.js';
	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'brendon-core-bible-books-table',
			get_template_directory_uri() . '/assets/js/bible-books-table.js',
			array(),
			filemtime( $js_path ),
			true
		);

		wp_localize_script(
			'brendon-core-bible-books-table',
			'bbBibleBooksTable',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'bb_bible_books_table' ),
				'loggedIn'  => is_user_logged_in(),
				'completed' => brendon_core_bible_books_table_completed(),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'brendon_core_bible_books_table_assets', 30 );
