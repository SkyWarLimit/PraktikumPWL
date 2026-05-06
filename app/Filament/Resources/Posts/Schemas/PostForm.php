<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Category;
use Dom\Text;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Group;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Group::make([
            // section 1 - post details
            Section::make('Post Details')
            ->description("Fill in the details of the post.")
            // -> icon(Heroicon::RocketLaunch)
            -> icon('heroicon-o-document-text')
            ->schema([
                Group::make([
                TextInput::make('title')
                    ->rules('required | min:3')
                    ->validationMessages([
                        'required' => 'Judul harus diisi.',
                        ]),
                TextInput::make('slug')
                    ->rules('required')
                    ->unique()
                    ->minLength(3)
                    ->validationMessages([
                        'unique' => 'Slug harus unik dan tidak boleh sama.',
                    ]),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->required()
                    // ->preload()
                    ->searchable()
                    ->validationMessages([
                        'required' => 'Kategori harus dipilih.',
                    ]),
                ColorPicker::make('color'),
                            ])->columns(2),

                MarkdownEditor::make('content'),
                    ]),

                // section 2 - image 
                Section::make('Image Upload')
                ->description("Upload an image for the post.")
                // -> icon(Heroicon::Photo)
                -> icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make("image")
                        ->disk('public')
                        ->directory('Posts')
                        ->required(),
                ]),
                ])->columnSpan(2),

                Group::make([
                // section 3 - meta
                Section::make('Meta Information')
                ->description("Additional information about the post.")
                // -> icon(Heroicon::InformationCircle)
                -> icon('heroicon-o-information-circle')
                ->schema([
                    // TagsInput::make('tags'),
                    Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
                    Checkbox::make('published'),
                    DateTimePicker::make('published_at'),
                    ]),
                ])->columnSpan(1),
            
            ])->columns(3);
    }
}
