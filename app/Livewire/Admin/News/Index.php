<?php

namespace App\Livewire\Admin\News;

use App\Models\Category;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\User;
use Livewire\Attributes\On;



class Index extends Component
{
    use WithFileUploads, WithPagination;

    public $post_id;
    public $title_en, $title_id;
    public $type = 'article';
    public $content_en, $content_id;
    public $media_url;
    public $photo_upload;
    public $existing_photo_url;
    public $published_at;
    public array $visibility_options = [];
    public $seo_title, $seo_keywords, $seo_description;
    public $source_name, $source_url, $source_favicon_url;
    public $slug;
    public $user_id;

    public $document_upload;
    public $existing_document_url;

    public $showModal = false;
    public $isEditMode = false;
    public string $search = '';
    
    public $showFilePicker = false; 
    public $driveThumbnailPath = null; 
    public $existingThumbnailUrl = null;
    
    public $thumbnail;


    public $categories;
    public array $selectedCategories = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }



    public function mount()
    {
        $this->categories = Category::whereNull('parent_id')->with('children')->get();
    }

    public function updatedSourceUrl($value)
    {
        try {
            $domain = parse_url($value, PHP_URL_HOST);
            if ($domain) {
                $this->source_favicon_url = 'https://www.google.com/s2/favicons?domain=' . $domain . '&sz=32';
            }
        } catch (\Exception $e) {
        }
    }

    public function render()
    {
        $posts = Post::with(['categories', 'author', 'media'])->latest()->paginate(15);
        return view('livewire.admin.news.index', ['posts' => $posts])->layout('layouts.app');
    }

   public function create()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditMode = false;
    
        // Reset state gambar
        $this->driveThumbnailPath = null;
        $this->existingThumbnailUrl = null;
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $post->id;
        $this->title_en = $post->getTranslation('title', 'en');
        $this->title_id = $post->getTranslation('title', 'id');
        $this->type = $post->type;
        $this->content_en = $post->getTranslation('content', 'en');
        $this->content_id = $post->getTranslation('content', 'id');
        $this->media_url = $post->media_url;
        $this->source_name = $post->source_name;
        $this->source_url = $post->source_url;
        $this->source_favicon_url = $post->source_favicon_url;
        $this->published_at = $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : null;

        $this->selectedCategories = $post->categories->pluck('id')->toArray();

        $this->visibility_options = $post->visibility_options ?? [];

        $seoMeta = $post->seo_meta ?? []; // Ambil data SEO atau array kosong jika null
        $this->seo_title = $seoMeta['title'] ?? '';
        $this->seo_keywords = $seoMeta['keywords'] ?? '';
        $this->seo_description = $seoMeta['description'] ?? '';

        $this->user_id = auth()->id();
        $this->existing_photo_url = $post->getFirstMediaUrl('featured', 'thumbnail');

        $this->existing_document_url = $post->getFirstMediaUrl('document');
        
        $this->driveThumbnailPath = $post->featured_image_drive_id;
        $this->existingThumbnailUrl = $post->thumbnail_url; // Menggunakan accessor model

        $this->isEditMode = true;
        $this->showModal = true;
        $this->dispatch('set-ckeditor-content', id: 'content_en', content: $this->content_en);
        $this->dispatch('set-ckeditor-content', id: 'content_id', content: $this->content_id);

        $this->dispatch('update-categories-selection', $this->selectedCategories);
    }
    
    // --- FILE PICKER LOGIC ---
    public function openDrivePicker()
    {
        $this->showFilePicker = true;
        $this->dispatch('open-modal', 'news-file-picker');
    }
    
    #[On('fileSelected')] // Jangan lupa import Livewire\Attributes\On di paling atas
    public function handleDriveSelection($data)
    {
        $this->driveThumbnailPath = $data['path'];
        $this->existingThumbnailUrl = $data['preview_url'];
        
        // Reset manual upload jika ada
        $this->thumbnail = null; 
        
        $this->showFilePicker = false;
        $this->dispatch('close-modal', 'news-file-picker');
    }

    public function save()
    {
        $this->validate([
            'title_en' => 'required|string|max:255',
            'title_id' => 'required|string|max:255',
            'type' => 'required|in:article,video,audio,press_release,kebijakan',

            'selectedCategories' => 'required|array|min:1',
            'selectedCategories.*' => 'exists:categories,id',

            'content_en' => ['nullable', 'string', Rule::requiredIf($this->type !== 'press_release')],
            'content_id' => ['nullable', 'string', Rule::requiredIf($this->type !== 'press_release')],
            'media_url' => ['nullable', 'url', Rule::requiredIf($this->type === 'video' || $this->type === 'audio')],
            'photo_upload' => 'nullable|image|max:2048', // 2MB Max

            'document_upload' => [
                // Wajib jika tipenya 'press_release' ATAU 'kebijakan' DAN ini adalah post baru
                Rule::requiredIf(fn() => in_array($this->type, ['press_release', 'kebijakan']) && !$this->post_id),
                'nullable',
                'file',
                'mimes:pdf,doc,docx', // Izinkan PDF, Word
                'max:20480', // Maks 20MB
            ],
        ]);
        $slug = Str::slug($this->title_en);

        $data = [
            'title' => ['en' => $this->title_en, 'id' => $this->title_id],
            'slug' => $slug,
            'type' => $this->type,
            'featured_image_drive_id' => $this->driveThumbnailPath,

            'content' => $this->type === 'press_release'
                ? ['en' => '', 'id' => '']
                : ['en' => $this->content_en, 'id' => $this->content_id],


            'media_url' => $this->type === 'press_release' ? null : $this->media_url,
            'source_name' => $this->type === 'press_release' ? null : $this->source_name,
            'source_url' => $this->type === 'press_release' ? null : $this->source_url,
            'source_favicon_url' => $this->type === 'press_release' ? null : $this->source_favicon_url,
            'published_at' => $this->published_at,

            'user_id' => auth()->id(),
            'visibility_options' => $this->visibility_options,
            'seo_meta' => [
                'title' => $this->seo_title,
                'keywords' => $this->seo_keywords,
                'description' => $this->seo_description,
            ],
        ];

        $post = Post::updateOrCreate(['id' => $this->post_id], $data);
        $post->categories()->sync($this->selectedCategories);

        if ($this->photo_upload) {
            $post->clearMediaCollection('featured');
            $post->addMedia($this->photo_upload->getRealPath())->toMediaCollection('featured');
        }

        if ($this->document_upload) {
            // Hapus dokumen lama jika ada
            $post->clearMediaCollection('document');
            // Tambahkan dokumen baru
            $post->addMedia($this->document_upload->getRealPath())->toMediaCollection('document');
        }
        
        if ($this->driveThumbnailPath) {
            // Hapus media lokal lama (jika ada) agar hemat space
            $post->clearMediaCollection('thumbnail');
        }
        
        elseif ($this->thumbnail) {
            // Hapus ID Drive dari database (karena user ganti ke manual)
            $post->update(['featured_image_drive_id' => null]);
    
            // Simpan file fisik
            $post->clearMediaCollection('thumbnail');
            $post->addMedia($this->thumbnail)->toMediaCollection('thumbnail');
        }

        $this->closeModal();
        session()->flash('message', 'Post successfully saved.');
    }

    public function delete($id)
    {
        Post::findOrFail($id)->delete();
        session()->flash('message', 'Post successfully deleted.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->post_id = null;
        $this->title_en = '';
        $this->title_id = '';
        $this->slug = '';
        $this->type = 'article';
        $this->content_en = '';
        $this->content_id = '';
        $this->media_url = '';
        $this->photo_upload = null;
        $this->existing_photo_url = null;
        $this->source_name = '';
        $this->source_url = '';
        $this->source_favicon_url = '';
        $this->published_at = now()->format('Y-m-d\TH:i');

        $this->document_upload = null;
        $this->existing_document_url = null;

        $this->selectedCategories = [];
        $this->dispatch('clear-categories-selection');
    }
}
