<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\FeedbackSubmission;
use App\Models\Registration;
use App\Models\BroadcastTemplate;
use App\Models\CheckinLog;
// use App\Models\InquiryForm;
// use App\Models\FeedbackForm;
use App\Models\EventEmailTemplate;
use App\Models\TicketTier;
use App\Models\Voucher;


class Event extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    // Mendefinisikan kolom yang bisa diterjemahkan
    public $translatable = ['name', 'description', 'venue', 'theme'];

    // protected $guarded = ['id'];


    // Mendefinisikan kolom yang boleh diisi
    protected $fillable = [
        'name',
        'slug',
        'theme',
        'description',
        'start_date',
        'end_date',
        'daily_schedules',
        'venue',
        'google_maps_iframe',
        'personnel',
        'sponsors',
        'quota',
        'is_active',
        'status',
        'visibility',
        'inquiry_form_id',
        'field_config',
        'requires_account',
        'type',
        'platform',
        'meeting_link',
        'meeting_info',
        'feedback_form_id',
        'confirmation_template_id',
        'youtube_recordings',
        'invitation_letter_body',
        'invitation_letter_header',
        'invitation_files',
        'invitation_wa_template',
        'invitation_email_subject',
        'invitation_email_body',
        'is_paid_event',
        'external_registration_link',

    ];

    protected $attributes = [
        'visibility' => 'public',
    ];

    protected $dates = ['start_date', 'end_date'];

    // Mengubah tipe data kolom secara otomatis
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'theme' => 'array',
        'personnel' => 'array',
        'sponsors' => 'array',
        'field_config' => 'array',
        'meeting_info' => 'array',
        'type' => 'string',
        'platform' => 'string',
        'confirmation_template_id' => 'integer',
        'feedback_form_id' => 'integer',
        'inquiry_form_id' => 'integer',
        'daily_schedules' => 'array',
        'requires_account' => 'boolean',
        'youtube_recordings' => 'array',
        'visibility' => 'string',
        'invitation_files' => 'array',
        'is_paid_event' => 'boolean',


    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    protected function remainingQuota(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->quota - $this->registrations()->count(),
        );
    }

    public function inquiryForm()
    {
        return $this->belongsTo(InquiryForm::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('card-banner')
            ->width(400)
            ->height(250)
            ->sharpen(10);

        $this->addMediaConversion('page-banner')
            ->width(1200)
            ->height(400)
            ->sharpen(10);
    }

    public function feedbackForm()
    {
        return $this->belongsTo(FeedbackForm::class, 'feedback_form_id');
    }

    public function confirmationTemplate()
    {
        return $this->belongsTo(EventEmailTemplate::class, 'confirmation_template_id');
    }

    public function feedbackSubmissions()
    {
        return $this->hasManyThrough(FeedbackSubmission::class, Registration::class);
    }

    public function broadcastTemplates()
    {
        return $this->hasMany(BroadcastTemplate::class);
    }

    public function emailTemplates()
    {
        return $this->hasMany(EventEmailTemplate::class);
    }

    public function checkinLogs()
    {
        return $this->hasManyThrough(CheckinLog::class, Registration::class);
    }

    // Jenis Tiket (Multi-tier)
    public function ticketTiers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketTier::class);
    }

    // Voucher khusus Event ini
    public function vouchers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Voucher::class);
    }
}
