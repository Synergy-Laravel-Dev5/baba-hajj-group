<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\HajjBook;
use App\Models\HujjajGuideline;
use App\Models\TutorialVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppContentController extends Controller
{
    /**
     * Hajj & Umrah Step-by-Step Guidelines & Rules
     */
    public function guides(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $query = HujjajGuideline::where('is_active', true)->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . trim($search) . '%')
                  ->orWhere('sub_heading', 'like', '%' . trim($search) . '%')
                  ->orWhere('content', 'like', '%' . trim($search) . '%');
            });
        }

        $guidelines = $query->get()->map(function ($item) {
            return [
                'id'          => $item->id,
                'title'       => $item->title,
                'sub_heading' => $item->sub_heading,
                'content'     => $item->content,
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Hajj & Umrah guidelines retrieved successfully.',
            'count'   => $guidelines->count(),
            'data'    => $guidelines,
        ], 200);
    }

    public function guideDetail($id): JsonResponse
    {
        $item = HujjajGuideline::where('is_active', true)->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Guideline not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $item->id,
                'title'       => $item->title,
                'sub_heading' => $item->sub_heading,
                'content'     => $item->content,
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

    /**
     * Video Tutorials & Live Streams
     */
    public function videos(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $query = TutorialVideo::where('is_active', true)->latest();

        if (!empty($search)) {
            $query->where('title', 'like', '%' . trim($search) . '%');
        }

        $videos = $query->get()->map(function ($v) {
            return [
                'id'            => $v->id,
                'title'         => $v->title,
                'description'   => $v->description,
                'thumbnail_url' => $v->thumbnail_url,
                'video_type'    => $v->video_type,
                'play_url'      => $v->play_url,
                'is_live'       => str_contains(strtolower($v->title . ' ' . $v->description), 'live'),
                'created_at'    => $v->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Video tutorials retrieved successfully.',
            'count'   => $videos->count(),
            'data'    => $videos,
        ], 200);
    }

    public function videoDetail($id): JsonResponse
    {
        $video = TutorialVideo::where('is_active', true)->find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $video->id,
                'title'         => $video->title,
                'description'   => $video->description,
                'thumbnail_url' => $video->thumbnail_url,
                'video_type'    => $video->video_type,
                'play_url'      => $video->play_url,
                'created_at'    => $video->created_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

    /**
     * Hajj Books & PDF Library
     */
    public function books(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $query = HajjBook::latest();

        if (!empty($search)) {
            $query->where('title', 'like', '%' . trim($search) . '%');
        }

        $books = $query->get()->map(function ($b) {
            return [
                'id'            => $b->id,
                'title'         => $b->title,
                'thumbnail_url' => $b->thumbnail_url,
                'file_url'      => $b->file_url,
                'file_type'     => $b->file_type,
                'file_size'     => $b->file_size,
                'created_at'    => $b->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Hajj books & library retrieved successfully.',
            'count'   => $books->count(),
            'data'    => $books,
        ], 200);
    }

    public function bookDetail($id): JsonResponse
    {
        $book = HajjBook::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $book->id,
                'title'         => $book->title,
                'thumbnail_url' => $book->thumbnail_url,
                'file_url'      => $book->file_url,
                'file_type'     => $book->file_type,
                'file_size'     => $book->file_size,
                'created_at'    => $book->created_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

    /**
     * Ziyarat & Holy Places
     */
    public function ziyarat(Request $request): JsonResponse
    {
        $category = $request->query('category');
        $search   = $request->query('search');

        $query = Gallery::with('images')->latest();

        if (!empty($category) && in_array($category, ['makkah', 'madina', 'other'])) {
            $query->where('category', $category);
        }

        if (!empty($search)) {
            $query->where('title', 'like', '%' . trim($search) . '%');
        }

        $items = $query->get()->map(function ($item) {
            return [
                'id'           => $item->id,
                'category'     => $item->category,
                'title'        => $item->title,
                'description'  => $item->description,
                'cover_image'  => $item->image_url,
                'total_images' => $item->images->count(),
                'created_at'   => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Ziyarat places retrieved successfully.',
            'count'   => $items->count(),
            'data'    => $items,
        ], 200);
    }

    public function ziyaratDetail($id): JsonResponse
    {
        $item = Gallery::with('images')->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Ziyarat item not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $item->id,
                'category'    => $item->category,
                'title'       => $item->title,
                'description' => $item->description,
                'cover_image' => $item->image_url,
                'images'      => $item->images->map(fn($img) => [
                    'id'         => $img->id,
                    'image_url'  => $img->image_url,
                    'is_primary' => (bool)$img->is_primary,
                ]),
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

    /**
     * Essential Hajj & Umrah Duas
     */
    public function duas(): JsonResponse
    {
        $duas = [
            [
                'id'              => 1,
                'category'        => 'Ihram',
                'title'           => 'Niyyah for Umrah (Ihram ki Niyat)',
                'arabic'          => 'لَبَّيْكَ اللَّهُمَّ عُمْرَةً',
                'transliteration' => "Labbayk Allahumma 'Umratan",
                'urdu'            => 'اے اللہ! میں عمرے کی نیت کرتا/کرتی ہوں، اسے میرے لیے آسان فرما اور قبول فرما۔',
                'english'         => 'Here I am, O Allah, for Umrah.',
            ],
            [
                'id'              => 2,
                'category'        => 'Talbiyah',
                'title'           => 'Talbiyah (تلبیہ)',
                'arabic'          => 'لَبَّيْكَ اللَّهُمَّ لَبَّيْكَ، لَبَّيْكَ لاَ شَرِيكَ لَكَ لَبَّيْكَ، إِنَّ الْحَمْدَ وَالنِّعْمَةَ لَكَ وَالْمُلْكَ، لاَ شَرِيكَ لَكَ',
                'transliteration' => "Labbayk Allahumma labbayk, labbayka la shareeka laka labbayk, innal-hamda wan-ni'mata laka wal-mulk, la shareeka lak",
                'urdu'            => 'حاضر ہوں اے اللہ! میں حاضر ہوں، تیرا کوئی شریک نہیں، تمام تعریفیں اور نعمتیں تیرے ہی لیے ہیں اور بادشاہی بھی، تیرا کوئی شریک نہیں۔',
                'english'         => 'Here I am, O Allah, here I am. Here I am, You have no partner, here I am. Verily all praise, grace, and sovereignty belong to You. You have no partner.',
            ],
            [
                'id'              => 3,
                'category'        => 'Tawaf',
                'title'           => 'Between Rukn Yamani and Hajar-e-Aswad',
                'arabic'          => 'رَبَّنَا آتِنَا فِي الدُّنْيَا حَسَنَةً وَفِي الآخِرَةِ حَسَنَةً وَقِنَا عَذَابَ النَّارِ',
                'transliteration' => "Rabbana atina fid-dunya hasanatan wa fil-akhirati hasanatan wa qina 'adhaban-nar",
                'urdu'            => 'اے ہمارے رب! ہمیں دنیا میں بھی بھلائی عطا فرما اور آخرت میں بھی بھلائی عطا فرما اور ہمیں جہنم کے عذاب سے بچا۔',
                'english'         => 'Our Lord, give us good in this world and good in the next world, and protect us from the punishment of the Fire.',
            ],
            [
                'id'              => 4,
                'category'        => "Sa'i",
                'title'           => "Dua at Safa and Marwah",
                'arabic'          => 'إِنَّ الصَّفَا وَالْمَرْوَةَ مِنْ شَعَائِرِ اللَّهِ ... أَبْدَأُ بِمَا بَدَأَ اللَّهُ بِهِ',
                'transliteration' => "Innas-Safa wal-Marwata min sha'a'irillah. Abda'u bima bada'Allahu bih",
                'urdu'            => 'بیشک صفا اور مروہ اللہ کی نشانیوں میں سے ہیں۔ میں اسی سے شروع کرتا ہوں جس سے اللہ نے شروع فرمایا۔',
                'english'         => 'Indeed, Safa and Marwah are among the symbols of Allah. I begin with that with which Allah began.',
            ],
            [
                'id'              => 5,
                'category'        => 'Arafat',
                'title'           => 'Dua of Arafat (عید عرفہ کی دعا)',
                'arabic'          => 'لاَ إِلَهَ إِلاَّ اللَّهُ وَحْدَهُ لاَ شَرِيكَ لَهُ، لَهُ الْمُلْكُ وَلَهُ الْحَمْدُ، وَهُوَ عَلَى كُلِّ شَيْءٍ قَدِيرٌ',
                'transliteration' => "La ilaha illallahu wahdahu la shareeka lah, lahul-mulku wa lahul-hamd, wa huwa 'ala kulli shay'in qadeer",
                'urdu'            => 'اللہ کے سوا کوئی معبود نہیں، وہ اکیلا ہے، اس کا کوئی شریک نہیں، اسی کے لیے بادشاہی ہے اور تمام تعریفیں، اور وہ ہر چیز پر قادر ہے۔',
                'english'         => 'There is no deity worthy of worship except Allah alone, without partner. To Him belongs kingdom and praise, and He has power over all things.',
            ],
            [
                'id'              => 6,
                'category'        => 'Madinah',
                'title'           => 'Salam on Holy Prophet (PBUH) at Rawdah',
                'arabic'          => 'السَّلاَمُ عَلَيْكَ يَا رَسُولَ اللَّهِ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ',
                'transliteration' => "Assalamu 'alayka ya RasoolAllah wa rahmatullahi wa barakatuh",
                'urdu'            => 'سلام ہو آپ پر اے اللہ کے رسول، اور اللہ کی رحمتیں اور برکتیں۔',
                'english'         => 'Peace be upon you, O Messenger of Allah, and the mercy of Allah and His blessings.',
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Duas retrieved successfully.',
            'count'   => count($duas),
            'data'    => $duas,
        ], 200);
    }

    /**
     * Important Helplines & Emergency Contacts
     */
    public function emergencyContacts(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Emergency contacts retrieved successfully.',
            'data'    => [
                'agency_contacts' => [
                    'title'          => 'Baba Hajj & Umrah Group Helplines',
                    'phone_primary'  => '+92 300 1234567',
                    'whatsapp'       => '+92 300 1234567',
                    'email'          => 'info@babahajjgroup.com',
                    'saudi_incharge' => '+966 50 123 4567',
                ],
                'saudi_emergency' => [
                    ['service' => 'Police (شرطة)', 'number' => '999'],
                    ['service' => 'Ambulance (إسعاف)', 'number' => '997'],
                    ['service' => 'Civil Defense / Fire (الدفاع المدني)', 'number' => '998'],
                    ['service' => 'Traffic Police (مرور)', 'number' => '993'],
                    ['service' => 'Ministry of Hajj & Umrah Call Center (وزارة الحج)', 'number' => '1966'],
                    ['service' => 'Unified Emergency Call (مركز العمليات الموحد)', 'number' => '911'],
                    ['service' => 'Haramain Train Helpline', 'number' => '920004724'],
                ],
            ]
        ], 200);
    }
}
