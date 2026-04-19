<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

class PublishPosts extends Command
{
    protected $signature = 'app:publish-posts';
    protected $description = 'Auto publish scheduled posts';

    public function handle()
    {
        Post::where('status', 'scheduled')
            ->where('publish_at', '<=', now())
            ->update(['status' => 'published']);

        $this->info('Checked scheduled posts!');
    }
}