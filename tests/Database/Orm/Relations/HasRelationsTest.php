<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Relations;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Relations\HasMany;
use Palet\Framework\Database\Orm\Relations\BelongsTo;
use Palet\Framework\Database\Orm\Model\BaseModel;
use Palet\Framework\Database\Orm\Model\ModelCollection;

class RelatablePost extends BaseModel
{
    public function user()
    {
        return new BelongsTo(new RelatableUser(), $this, 'user_id', 'id');
    }
}

class RelatableUser extends BaseModel
{
    protected array $guarded = [];

    public function posts()
    {
        return new HasMany(new RelatablePost(), $this, 'user_id', 'id');
    }
}

class HasRelationsTest extends TestCase
{
    public function test_lazy_loads_relation_via_magic_property()
    {
        $user = new RelatableUser();
        
        $this->assertFalse($user->relationLoaded('posts'));
        
        // Accessing magic property triggers lazy loading
        $posts = $user->posts;
        
        $this->assertInstanceOf(ModelCollection::class, $posts);
        $this->assertTrue($user->relationLoaded('posts'));
        $this->assertSame($posts, $user->getRelation('posts'));
    }
    
    public function test_lazy_loads_single_relation()
    {
        $post = new RelatablePost();
        
        $this->assertFalse($post->relationLoaded('user'));
        
        $user = $post->user;
        
        $this->assertInstanceOf(RelatableUser::class, $user);
        $this->assertTrue($post->relationLoaded('user'));
    }
    
    public function test_to_array_includes_loaded_relations()
    {
        $user = new RelatableUser(['name' => 'John']);
        
        $user->posts; // lazy load
        
        $array = $user->toArray();
        
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('posts', $array);
        $this->assertIsArray($array['posts']);
    }
}
