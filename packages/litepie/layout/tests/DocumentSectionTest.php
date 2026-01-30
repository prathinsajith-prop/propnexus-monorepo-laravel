<?php

namespace Tests;

use Litepie\Layout\Components\DocumentSection;
use Litepie\Layout\LayoutBuilder;
use PHPUnit\Framework\TestCase;

class DocumentSectionTest extends TestCase
{
    /** @test */
    public function it_can_create_a_document_upload_section()
    {
        $section = DocumentSection::make('file_upload')
            ->title('Upload Files')
            ->upload()
            ->multiple()
            ->dragDrop()
            ->allowedTypes(['pdf', 'doc', 'docx'])
            ->maxSize(10)
            ->maxFiles(5)
            ->uploadUrl('/api/upload');

        $array = $section->toArray();

        $this->assertEquals('document', $array['type']);
        $this->assertEquals('file_upload', $array['name']);
        $this->assertEquals('Upload Files', $array['title']);
        $this->assertEquals('upload', $array['document_type']);
        $this->assertTrue($array['multiple']);
        $this->assertTrue($array['drag_drop']);
        $this->assertEquals(['pdf', 'doc', 'docx'], $array['allowed_types']);
        $this->assertEquals(10, $array['max_size']);
        $this->assertEquals(5, $array['max_files']);
        $this->assertEquals('/api/upload', $array['upload_url']);
    }

    /** @test */
    public function it_can_create_a_document_list_section()
    {
        $section = DocumentSection::make('document_list')
            ->title('My Documents')
            ->list()
            ->table()
            ->searchable()
            ->sortable()
            ->showPreview()
            ->showSize()
            ->showDate()
            ->showActions()
            ->dataUrl('/api/documents')
            ->deleteUrl('/api/documents/{id}')
            ->downloadUrl('/api/documents/{id}/download');

        $array = $section->toArray();

        $this->assertEquals('document', $array['type']);
        $this->assertEquals('document_list', $array['name']);
        $this->assertEquals('list', $array['document_type']);
        $this->assertEquals('table', $array['display_mode']);
        $this->assertTrue($array['searchable']);
        $this->assertTrue($array['sortable']);
        $this->assertTrue($array['show_preview']);
        $this->assertTrue($array['show_size']);
        $this->assertTrue($array['show_date']);
        $this->assertTrue($array['show_actions']);
        $this->assertEquals('/api/documents', $array['data_url']);
        $this->assertEquals('/api/documents/{id}', $array['delete_url']);
        $this->assertEquals('/api/documents/{id}/download', $array['download_url']);
    }

    /** @test */
    public function it_can_add_columns_to_table_view()
    {
        $section = DocumentSection::make('docs')
            ->list()
            ->table()
            ->addColumn('name', 'File Name', ['sortable' => true, 'width' => '40%'])
            ->addColumn('size', 'Size', ['sortable' => true, 'width' => '20%'])
            ->addColumn('date', 'Upload Date', ['sortable' => false, 'width' => '20%']);

        $array = $section->toArray();

        $this->assertCount(3, $array['columns']);
        $this->assertEquals('name', $array['columns'][0]['key']);
        $this->assertEquals('File Name', $array['columns'][0]['label']);
        $this->assertTrue($array['columns'][0]['sortable']);
        $this->assertEquals('40%', $array['columns'][0]['width']);
    }

    /** @test */
    public function it_can_add_filters()
    {
        $section = DocumentSection::make('docs')
            ->list()
            ->addFilter('type', 'File Type', [
                'type' => 'select',
                'options' => ['pdf' => 'PDF', 'doc' => 'Word'],
            ])
            ->addFilter('date', 'Upload Date', [
                'type' => 'daterange',
                'options' => [],
            ]);

        $array = $section->toArray();

        $this->assertCount(2, $array['filters']);
        $this->assertEquals('type', $array['filters'][0]['key']);
        $this->assertEquals('File Type', $array['filters'][0]['label']);
        $this->assertEquals('select', $array['filters'][0]['type']);
    }

    /** @test */
    public function it_can_set_display_modes()
    {
        $tableSection = DocumentSection::make('docs')->list()->table();
        $this->assertEquals('table', $tableSection->toArray()['display_mode']);

        $gridSection = DocumentSection::make('docs')->list()->grid();
        $this->assertEquals('grid', $gridSection->toArray()['display_mode']);

        $listSection = DocumentSection::make('docs')->list()->listMode();
        $this->assertEquals('list', $listSection->toArray()['display_mode']);
    }

    /** @test */
    public function it_can_be_created_via_layout_builder()
    {
        $layout = LayoutBuilder::create('documents', 'manage')
            ->section('document', 'uploader')
            ->title('Upload')
            ->upload()
            ->multiple()
            ->allowedTypes(['pdf'])
            ->uploadUrl('/api/upload')
            ->endSection()
            ->build();

        $array = $layout->toArray();

        $this->assertCount(1, $array['sections']);
        $this->assertEquals('document', $array['sections'][0]['type']);
        $this->assertEquals('uploader', $array['sections'][0]['name']);
        $this->assertEquals('upload', $array['sections'][0]['document_type']);
    }

    /** @test */
    public function it_supports_viewer_mode()
    {
        $section = DocumentSection::make('viewer')
            ->viewer()
            ->showPreview()
            ->dataUrl('/api/document/123');

        $array = $section->toArray();

        $this->assertEquals('viewer', $array['document_type']);
        $this->assertTrue($array['show_preview']);
    }

    /** @test */
    public function it_can_add_document_items()
    {
        $section = DocumentSection::make('docs')
            ->addItem('doc1', [
                'name' => 'Report.pdf',
                'type' => 'pdf',
                'size' => 1024000,
                'url' => '/files/report.pdf',
            ])
            ->addItem('doc2', [
                'name' => 'Presentation.pptx',
                'type' => 'pptx',
                'size' => 2048000,
                'url' => '/files/presentation.pptx',
            ]);

        $array = $section->toArray();

        $this->assertCount(2, $array['items']);
        $this->assertEquals('doc1', $array['items'][0]['key']);
        $this->assertEquals('Report.pdf', $array['items'][0]['name']);
        $this->assertEquals('pdf', $array['items'][0]['type']);
    }

    /** @test */
    public function it_validates_file_size_and_count()
    {
        $section = DocumentSection::make('upload')
            ->upload()
            ->maxSize(25)
            ->maxFiles(10);

        $array = $section->toArray();

        $this->assertEquals(25, $array['max_size']);
        $this->assertEquals(10, $array['max_files']);
    }

    /** @test */
    public function it_supports_all_visibility_options()
    {
        $section = DocumentSection::make('docs')
            ->list()
            ->showPreview(true)
            ->showSize(true)
            ->showDate(true)
            ->showActions(true);

        $array = $section->toArray();

        $this->assertTrue($array['show_preview']);
        $this->assertTrue($array['show_size']);
        $this->assertTrue($array['show_date']);
        $this->assertTrue($array['show_actions']);
    }

    /** @test */
    public function it_can_disable_visibility_options()
    {
        $section = DocumentSection::make('docs')
            ->list()
            ->showPreview(false)
            ->showSize(false)
            ->showDate(false)
            ->showActions(false);

        $array = $section->toArray();

        $this->assertFalse($array['show_preview']);
        $this->assertFalse($array['show_size']);
        $this->assertFalse($array['show_date']);
        $this->assertFalse($array['show_actions']);
    }
}
