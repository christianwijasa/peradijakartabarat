<?php

namespace App\Support;

use App\Models\CandidateAdvocate;
use App\Models\VerificationChecklist;

final class CandidateVerificationChecklist
{
    /**
     * @return list<array{label: string, requires_upload: bool}>
     */
    public static function defaultItems(): array
    {
        return [
            ['label' => 'Sertifikat PKPA cocok data admisi', 'requires_upload' => true],
            ['label' => 'Sertifikat Lulus UPA terverifikasi', 'requires_upload' => true],
            ['label' => 'Ijazah S.H. terbaca jelas', 'requires_upload' => true],
            ['label' => 'Pasfoto latar merah sesuai ketentuan', 'requires_upload' => true],
            ['label' => 'Data profil lengkap (NIK & universitas)', 'requires_upload' => false],
            ['label' => 'Status keanggotaan DPC aktif', 'requires_upload' => false],
        ];
    }

    public static function seedFor(CandidateAdvocate $candidateAdvocate): void
    {
        if ($candidateAdvocate->checklistItems()->exists()) {
            return;
        }

        $now = now();
        $rows = [];
        foreach (self::defaultItems() as $item) {
            $rows[] = [
                'checkable_type' => CandidateAdvocate::class,
                'checkable_id' => $candidateAdvocate->id,
                'label' => $item['label'],
                'file_path' => null,
                'file_size_label' => null,
                'is_checked' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        VerificationChecklist::insert($rows);

        self::syncProfileItem($candidateAdvocate->fresh());
    }

    public static function syncProfileItem(CandidateAdvocate $candidateAdvocate): void
    {
        $item = $candidateAdvocate->checklistItems()
            ->where('label', 'Data profil lengkap (NIK & universitas)')
            ->first();

        if (! $item) {
            return;
        }

        $complete = filled($candidateAdvocate->national_id_number) && filled($candidateAdvocate->university);
        if ($complete && ! $item->is_checked) {
            $item->update(['is_checked' => true]);
        }
    }

    public static function pendingUploadCount(CandidateAdvocate $candidateAdvocate): int
    {
        $uploadLabels = self::uploadLabels();

        return $candidateAdvocate->checklistItems()
            ->whereIn('label', $uploadLabels)
            ->where(function ($query) {
                $query->whereNull('document_url')->whereNull('file_path');
            })
            ->count();
    }

    /**
     * @return list<string>
     */
    public static function uploadLabels(): array
    {
        return collect(self::defaultItems())
            ->filter(fn ($i) => $i['requires_upload'])
            ->pluck('label')
            ->all();
    }

    public static function requiresUpload(string $label): bool
    {
        return in_array($label, self::uploadLabels(), true);
    }

    /** Ensure every calon in the admin queue has the same checklist rows (same labels, same order). */
    public static function syncStandardItems(CandidateAdvocate $candidateAdvocate): void
    {
        foreach (self::defaultItems() as $item) {
            VerificationChecklist::firstOrCreate(
                [
                    'checkable_type' => CandidateAdvocate::class,
                    'checkable_id' => $candidateAdvocate->id,
                    'label' => $item['label'],
                ],
                [
                    'is_checked' => false,
                    'file_path' => null,
                    'file_size_label' => null,
                ]
            );
        }

        self::syncProfileItem($candidateAdvocate->fresh());
    }

    /**
     * Demo seeder: replace checklist with the standard set and preset is_checked / file hints.
     *
     * @param  array<string, bool>  $checkedByLabel
     * @param  array<string, string|null>  $fileSizeByLabel
     */
    public static function seedDemoState(
        CandidateAdvocate $candidateAdvocate,
        array $checkedByLabel,
        array $fileSizeByLabel = [],
    ): void {
        $candidateAdvocate->checklistItems()->delete();

        $now = now();
        $rows = [];
        foreach (self::defaultItems() as $item) {
            $label = $item['label'];
            $hasDoc = $item['requires_upload'] && ($checkedByLabel[$label] ?? false);
            $rows[] = [
                'checkable_type' => CandidateAdvocate::class,
                'checkable_id' => $candidateAdvocate->id,
                'label' => $label,
                'file_path' => null,
                'file_size_label' => null,
                'document_url' => $hasDoc
                    ? 'https://drive.google.com/file/d/demo-'.$candidateAdvocate->id.'-'.md5($label).'/view'
                    : null,
                'is_checked' => $checkedByLabel[$label] ?? false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        VerificationChecklist::insert($rows);
    }

    /** @return 'belum_lengkap'|'menunggu_review'|'siap' */
    public static function adminQueueStatus(CandidateAdvocate $candidateAdvocate): string
    {
        self::syncStandardItems($candidateAdvocate);

        $items = $candidateAdvocate->fresh()->checklistItems->keyBy('label');

        foreach (self::defaultItems() as $def) {
            $item = $items->get($def['label']);
            if (! $item) {
                return 'belum_lengkap';
            }
            if ($def['requires_upload'] && ! $item->hasDocumentReference() && ! $item->is_checked) {
                return 'belum_lengkap';
            }
        }

        $allChecked = collect(self::defaultItems())->every(
            fn ($def) => ($items->get($def['label'])?->is_checked ?? false)
        );

        return $allChecked ? 'siap' : 'menunggu_review';
    }
}
