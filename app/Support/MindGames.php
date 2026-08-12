<?php

namespace App\Support;

/**
 * Curated brain-training games/exercises that build logical thinking —
 * the antidote to AI-era mental laziness. Stored as data (easy to extend).
 */
class MindGames
{
    /** @return array<int, array{name:string, emoji:string, trains:string, tip:string}> */
    public static function all(): array
    {
        return [
            ['name' => 'الشطرنج', 'emoji' => '♟️', 'trains' => 'التخطيط، استشراف العواقب، والصبر', 'tip' => 'العب على chess.com أو Lichess، وحُلّ puzzle يوميًا.'],
            ['name' => 'سودوكو', 'emoji' => '🔢', 'trains' => 'الاستنتاج المنطقي والتركيز', 'tip' => 'ابدأ سهل واطلع للصعب بالتدريج.'],
            ['name' => 'ألغاز منطقية', 'emoji' => '🧩', 'trains' => 'الاستدلال وربط المعطيات', 'tip' => 'ألغاز مثل «من يملك السمكة؟» (Einstein’s riddle).'],
            ['name' => 'حساب ذهني', 'emoji' => '➗', 'trains' => 'الرشاقة العقلية وسرعة المعالجة', 'tip' => 'احسب من غير آلة حاسبة، وزوّد الصعوبة.'],
            ['name' => 'Go (جو)', 'emoji' => '⚫', 'trains' => 'الرؤية الشاملة والاستراتيجية', 'tip' => 'ابدأ برقعة 9×9 لتتعلّم الأساسيات.'],
            ['name' => 'كلمات متقاطعة', 'emoji' => '📝', 'trains' => 'اللغة والاسترجاع المرن', 'tip' => 'جرّب متقاطعات عربية وإنجليزية.'],
            ['name' => 'مسائل خوارزميات', 'emoji' => '💻', 'trains' => 'تفكيك المشكلات والتفكير الحسابي', 'tip' => 'مسألة LeetCode/Codeforces سهلة كل يوم.'],
            ['name' => 'N-back / ذاكرة عاملة', 'emoji' => '🃏', 'trains' => 'الذاكرة العاملة والانتباه', 'tip' => 'تطبيقات Dual N-back، 10 دقايق كفاية.'],
            ['name' => 'قراءة وتحليل نقدي', 'emoji' => '🔍', 'trains' => 'الفهم العميق والتفكير النقدي', 'tip' => 'اقرأ مقال وحاول تلخّصه وتنقده بنفسك قبل أي AI.'],
            ['name' => 'ألغاز رياضية', 'emoji' => '🎯', 'trains' => 'الإبداع في الحل والتفكير الجانبي', 'tip' => 'Project Euler أو ألغاز الأولمبياد.'],
            ['name' => 'الداما / Checkers', 'emoji' => '♜', 'trains' => 'التخطيط قصير المدى', 'tip' => 'بداية بسيطة قبل الشطرنج.'],
            ['name' => 'حفظ وتلاوة بفهم', 'emoji' => '📖', 'trains' => 'الذاكرة العميقة والتركيز', 'tip' => 'احفظ بفهم المعنى مش تكرار أعمى.'],
        ];
    }
}
