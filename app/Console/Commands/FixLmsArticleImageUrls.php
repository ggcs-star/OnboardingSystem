<?php

namespace App\Console\Commands;

use App\Models\LmsArticle;
use Illuminate\Console\Command;

class FixLmsArticleImageUrls extends Command
{
    protected $signature = 'lms:fix-article-image-urls';

    protected $description = 'Rewrite broken image URLs (page-relative "../storage/..." or host-baked "http://.../storage/...") in LMS article content to root-relative "/storage/..." URLs';

    public function handle(): int
    {
        $pattern = '#src="(?:https?://[^"/]+)?/?(?:(?:\.\./)+)?storage/#i';
        $fixed = 0;

        LmsArticle::whereNotNull('content')->get(['id', 'content'])->each(function (LmsArticle $article) use ($pattern, &$fixed) {
            $original = $article->content;
            $updated = preg_replace($pattern, 'src="/storage/', $original);

            if ($updated !== $original) {
                $article->update(['content' => $updated]);
                $fixed++;
                $this->line("Fixed article #{$article->id}");
            }
        });

        $this->info("Total articles fixed: {$fixed}");

        return self::SUCCESS;
    }
}
