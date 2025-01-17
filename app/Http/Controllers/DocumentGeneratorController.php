<?php

namespace App\Http\Controllers;

use App\Models\HRDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Http\Request;

class DocumentGeneratorController extends Controller
{
    function GeneratorDoc()
    {
        $userId = Auth::id();
        $parentLink = 'Document Generator';
        $link = 'Document';

        $employeeId = auth()->user()->employee_id;

        $documents = HRDocument::where('employee_id', $employeeId)->get();
        // dd($documents);

        return view('hcis.document.generatorDocUpload', [
            'userId' => $userId,
            'documents' => $documents,
            'parentLink' => $parentLink,
            'link' => $link
        ]);
    }

    public function GeneratorUpload(Request $request)
    { 
        $userId = Auth::id();  
        $employeeId = auth()->user()->employee_id;

        // Validasi request
        $request->validate([
            'letter_name' => 'required|string',
            'template' => 'required|file|mimes:docx'
        ]);

        try {
           // Baca file DOCX
            $templateFile = $request->file('template');
            $phpWord = IOFactory::load($templateFile->getPathname());

            // Ekstrak teks dari dokumen
            $templateVariables = [];
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text = $element->getText();
                        // Cari semua pattern ${variable}
                        preg_match_all('/\$\{([^}]+)\}/', $text, $matches);
                        if (!empty($matches[1])) {
                            foreach ($matches[1] as $variable) {
                                $templateVariables[] = $variable;
                            }
                        }
                    }
                }
            }

            // Hapus duplikat variabel
            $templateVariables = array_unique($templateVariables);

            // Buat direktori jika belum ada
            $templateDirectory = public_path('templates/result');
            if (!file_exists($templateDirectory)) {
                mkdir($templateDirectory, 0777, true);
            }

            // Generate nama file yang unik
            $fileName = $request->letter_name . '_' . time() . '.docx';
            $templatePath = 'templates/result/' . $fileName;
            
            // Simpan file ke public folder
            $templateFile->move(public_path('templates/result'), $fileName);

            // Simpan ke database
            $document = new HRDocument();
            $document->letter_name = $request->letter_name;
            $document->template_path = $templatePath;
            $document->variables = json_encode($templateVariables);
            $document->created_by = $userId;
            $document->employee_id = $employeeId;
            $document->save();

            return redirect()->back()->with('success', 'Document template uploaded successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error uploading document: ' . $e->getMessage());
        }
    }

    public function GeneratorEdit(Request $request, $id)
    {
        try {
            $userId = Auth::id();
            $parentLink = 'Document Generator';
            $link = 'Document';
            
            // Ambil data dokumen
            $document = HRDocument::findOrFail($id);
            
            // Validasi jika dokumen tidak ditemukan
            if (!$document) {
                return redirect()->back()->with('error', 'Document not found');
            }
            
            // Validasi jika file template tidak ada
            if (!file_exists(public_path($document->template_path))) {
                return redirect()->back()->with('error', 'Template file not found');
            }
            
            // Gunakan file template yang sudah ada
            $template_path = $document->template_path;
            
            // Load template dan ambil variabel menggunakan TemplateProcessor
            try {
                $templateProcessor = new TemplateProcessor(public_path($template_path));
                $placeholders = $templateProcessor->getVariables();
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error reading template file: ' . $e->getMessage());
            }

            return view('hcis.document.generatorDocEdit', [
                'userId' => $userId,
                'parentLink' => $parentLink,
                'link' => $link,
                'letter_name' => $document->letter_name,
                'template_path' => $template_path,
                'document' => $document,
                'placeholders' => $placeholders
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error editing document: ' . $e->getMessage());
        }
    }

    public function GeneratorPreview(Request $request)
    {
        // Validasi input
        $request->validate([
            'template_path' => 'required|string',
            'letter_name' => 'required|string',
        ]);

        // Path lengkap ke template
        $templatePath = public_path($request->input('template_path'));

        // Load file DOCX menggunakan TemplateProcessor
        $templateProcessor = new TemplateProcessor($templatePath);

        // Ganti placeholder dengan nilai dari form, tapi dalam format ${value}
        foreach ($request->except(['_token', 'template_path', 'letter_name']) as $key => $value) {
            $templateProcessor->setValue($key, '${' . $value . '}');
        }

        // Buat direktori preview jika belum ada
        if (!file_exists(public_path('templates/preview'))) {
            mkdir(public_path('templates/preview'), 0777, true);
        }

        // Simpan hasil preview
        $previewPath = 'templates/preview/' . uniqid() . '_preview.docx';
        $templateProcessor->saveAs(public_path($previewPath));

        // Di controller preview, tambahkan cleanup file lama
        $files = glob(public_path('templates/preview/*'));
        foreach($files as $file) {
            if(is_file($file)) {
                // Hapus file yang lebih dari 1 jam
                if(time() - filemtime($file) >= 3600) {
                    unlink($file);
                }
            }
        }

        return response()->json([
            'success' => true,
            'preview_path' => $previewPath
        ]);
    }

    public function GeneratorDownload(Request $request)
    {
        $request->validate([
            'template_path' => 'required|string',
            'letter_name' => 'required|string',
        ]);

        // Path lengkap ke template
        $templatePath = public_path($request->input('template_path'));

        // Validasi apakah file ada dan valid
        if (!is_file($templatePath)) {
            throw new \Exception("Template file not found or is not a valid file: $templatePath");
        }

        try {
            // Load file DOCX menggunakan TemplateProcessor
            $templateProcessor = new TemplateProcessor($templatePath);
        } catch (\Exception $e) {
            throw new \Exception("Failed to process template file: " . $e->getMessage());
        }

        // Ganti placeholder dengan nilai dari form
        foreach ($request->except(['_token', 'template_path', 'letter_name']) as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Buat direktori jika belum ada
        $resultDir = public_path('templates/result');
        if (!file_exists($resultDir)) {
            mkdir($resultDir, 0777, true);
        }

        // Simpan hasil file yang sudah dimodifikasi
        $resultPath = $resultDir . '/' . $request->input('letter_name') . '_generated.docx';
        $templateProcessor->saveAs($resultPath);

        // Hapus file template sementara
        Storage::disk('public')->delete($request->input('template_path'));

        // Kirimkan file hasil untuk diunduh
        return response()->download($resultPath)->deleteFileAfterSend(true);
    }

    public function GeneratorDelete($id)
    {
        try {
            // Cari dokumen berdasarkan ID
            $document = HRDocument::findOrFail($id);

            // Hapus file template jika ada
            if ($document->template_path && file_exists(public_path($document->template_path))) {
                unlink(public_path($document->template_path));
                
                // Hapus folder jika kosong
                $folderPath = dirname(public_path($document->template_path));
                if (is_dir($folderPath) && count(scandir($folderPath)) <= 2) { // 2 karena . dan ..
                    rmdir($folderPath);
                }
            }

            // Hapus record dari database
            $document->delete();

            return redirect()->route('docGenerator')
                ->with('success', 'Document template deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('docGenerator')
                ->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }

}
