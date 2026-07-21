<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Every existing document field predates grouping, so fold them all
        // into a single "General" group per product before converting rows.
        $productIds = DB::table('product_document_fields')
            ->whereNull('product_document_group_id')
            ->distinct()
            ->pluck('product_id');

        foreach ($productIds as $productId) {
            $groupId = DB::table('product_document_groups')->insertGetId([
                'product_id' => $productId,
                'name' => 'General',
                'slug' => 'general',
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('product_document_fields')
                ->where('product_id', $productId)
                ->whereNull('product_document_group_id')
                ->update(['product_document_group_id' => $groupId]);
        }

        $fields = DB::table('product_document_fields')
            ->join('product_document_groups', 'product_document_groups.id', '=', 'product_document_fields.product_document_group_id')
            ->select('product_document_fields.*', 'product_document_groups.name as group_name', 'product_document_groups.slug as group_slug')
            ->get()
            ->keyBy('id');

        $values = DB::table('project_document_values')->orderBy('id')->get();

        $documentsByProject = [];

        foreach ($values as $value) {
            $field = $fields->get($value->document_field_id);

            if (! $field) {
                continue;
            }

            $documentsByProject[$value->project_id][$field->group_slug]['label'] = $field->group_name;
            $documentsByProject[$value->project_id][$field->group_slug]['fields'][$field->field_key] = [
                'label' => $field->label,
                'type' => $field->field_type,
                'required' => (bool) $field->required,
                'value' => $value->value,
                'file' => $value->file,
                'status' => $value->status,
                'remarks' => $value->remarks,
                'approved_by' => $value->approved_by,
                'approved_at' => $value->approved_at,
                'submitted_at' => $value->updated_at,
            ];
        }

        foreach ($documentsByProject as $projectId => $documents) {
            DB::table('projects')->where('id', $projectId)->update([
                'documents' => json_encode($documents),
            ]);
        }

        Schema::dropIfExists('project_document_values');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('project_document_values', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_field_id')->constrained('product_document_fields')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->string('file')->nullable();
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }
};
