<?php

namespace App\Services;

use App\Contracts\Tag\TagServiceInterface;
use App\Dto\DefaultResponseDto;
use App\Dto\Tag\TagCreateDto;
use App\Dto\Tag\TagGetDto;
use App\Enum\MessageEnum;
use App\Models\Tag;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagService implements TagServiceInterface
{
    use JsonResponseTrait;

    protected Tag $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Get all tags
     *
     * @param TagGetDto $payload
     * @return DefaultResponseDto
     */
    public function getTags(TagGetDto $payload): DefaultResponseDto
    {
        try {
            $tags = $this->tag->query()
                ->where('name', 'like', '%' . $payload->search . '%')
                ->orderBy('name', 'asc')
                ->paginate($payload->perPage, page: $payload->page);

            $response = [
                'content' => $tags->items(),
                'pagination' => [
                    'current_page' => $tags->currentPage(),
                    'per_page' => $tags->perPage(),
                    'total' => $tags->total(),
                    'prev_page' => $tags->currentPage() === 1 ? null : $tags->currentPage() - 1,
                    'next_page' => $tags->perPage() * $tags->currentPage() >= $tags->total() ? null : $tags->currentPage() + 1,
                ]
            ];

            return $this->successResponseDto(
                printf(MessageEnum::SUCCESS_MESSAGE, 'fetch tags'),
                $response
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(printf(MessageEnum::ERROR_EXECPTION, 'fetch tags'));

            return $this->errorResponseDto(
                printf(MessageEnum::FAILED_MESSAGE, 'fetch tags')
            );
        }
    }

    /**
     * Create a new tag
     *
     * @param TagCreateDto $payload
     * @return DefaultResponseDto
     */
    public function createTag(TagCreateDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            // check name if already exists
            $exists = $this->tag->query()
                ->where('name', $payload->name)
                ->exists();

            if ($exists) {
                return $this->errorResponseDto(
                    printf(MessageEnum::DATA_ALREADY_EXISTS, 'tag')
                );
            }

            $tag = $this->tag->create([
                'name' => $payload->name,
                'description' => $payload->description
            ]);

            DB::commit();

            return $this->successResponseDto(
                printf(MessageEnum::SUCCESS_MESSAGE, 'create tag'),
                $tag
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(printf(MessageEnum::ERROR_EXECPTION, 'creating tag'));

            DB::rollBack();

            return $this->errorResponseDto(
                printf(MessageEnum::FAILED_MESSAGE, 'create tag')
            );
        }
    }

    /**
     * Show a tag
     *
     * @param int $id
     * @return DefaultResponseDto
     */
    public function showTag(int $id): DefaultResponseDto
    {
        try {
            $tag = $this->tag->find($id);

            if (!$tag) {
                return $this->errorResponseDto(MessageEnum::NOT_FOUND);
            }

            return $this->successResponseDto(
                printf(MessageEnum::SUCCESS_MESSAGE, 'show tag'),
                $tag
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(printf(MessageEnum::ERROR_EXECPTION, 'showing tag'));

            return $this->errorResponseDto(
                printf(MessageEnum::FAILED_MESSAGE, 'show tag')
            );
        }
    }

    /**
     * Update a tag
     *
     * @param int $id
     * @param TagCreateDto $payload
     * @return DefaultResponseDto
     */
    public function updateTag(int $id, TagCreateDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $tag = $this->tag->find($id);

            if (!$tag) {
                return $this->errorResponseDto(MessageEnum::NOT_FOUND);
            }

            $exists = $this->tag->query()
                ->where('name', $payload->name)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return $this->errorResponseDto(
                    printf(MessageEnum::DATA_ALREADY_EXISTS, 'tag')
                );
            }

            $tag->update([
                'name' => $payload->name,
                'description' => $payload->description
            ]);

            DB::commit();

            return $this->successResponseDto(
                printf(MessageEnum::SUCCESS_MESSAGE, 'update tag'),
                $tag
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(printf(MessageEnum::ERROR_EXECPTION, 'updating tag'));

            DB::rollBack();

            return $this->errorResponseDto(
                printf(MessageEnum::FAILED_MESSAGE, 'update tag')
            );
        }
    }

    /**
     * Delete a tag
     *
     * @param int $id
     * @return DefaultResponseDto
     */
    public function deleteTag(int $id): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $tag = $this->tag->find($id);

            if (!$tag) {
                return $this->errorResponseDto(MessageEnum::NOT_FOUND);
            }

            $tag->delete();

            DB::commit();

            return $this->successResponseDto(
                printf(MessageEnum::SUCCESS_MESSAGE, 'delete tag'),
                $tag
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(printf(MessageEnum::ERROR_EXECPTION, 'deleting tag'));

            DB::rollBack();

            return $this->errorResponseDto(
                printf(MessageEnum::FAILED_MESSAGE, 'delete tag')
            );
        }
    }
}
