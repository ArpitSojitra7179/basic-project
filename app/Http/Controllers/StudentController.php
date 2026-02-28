<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use App\Models\Student;
use App\Models\Course;
use App\Mail\StudentCoursesDetails;

class StudentController extends Controller
{
    public function index()
    {
        try {
            $student = Student::all();

            return response()->json([
                'Student Details' => $student->chunk(5),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'nullable|email',
                'is_active' => 'nullable|string',
                'is_scholarship' => 'nullable|string',
            ]);

            $student = Student::create($validated);

            \Log::info("data inserted successfully.");

            return response()->json([
                'message' => 'Data inserted successfully.',
                'Student' => $student,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }


    public function appendExample()
    {
        try {
            $student = Student::all();

            $student->append('full_name');

            return response()->json([
                'FullName' => $student,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function containsExample()
    {
        try {
            $student = Student::all();

            $name = $student->contains('first_name', 'Tosif');

            return response()->json([
                'contains first_name' => $name,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function diffExample()
    {
        try {
            $allStudent = Student::all();

            $lastName = Student::where('last_name', '=', 'Bharvad')->get();

            $nonLastName = $allStudent->diff($lastName);

            return response()->json([
                'last name' => $nonLastName->pluck('last_name'),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function exceptExample()
    {
        try {
            $student = Student::all();

            $filtered = $student->except([1, 2]);

            return response()->json([
                'Students id' => $filtered->pluck('id'),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function intersectExample()
    {
        try {
            $student = Student::where('last_name', 'Bharvad')->get();

            $active = Student::where('is_active', 'true')->get();

            $studentDetails = $student->intersect($active);

            return response()->json([
                'Students last_name' => $studentDetails->pluck('last_name', 'first_name'),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function modelKeysExample()
    {
        try {
            $student = Student::all();

            $ids = $student->modelKeys();

            return response()->json([
                'student id' => $ids,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function makeVisibleExample()
    {
        try {
            $student = Student::all();

            $stdVisible = $student->makeVisible('is_scholarship');

            return response()->json([
                'show hidden details' => $stdVisible,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function makeHiddenExample()
    {
        try {
            $student = Student::all();

            $stdHidden = $student->makeHidden('last_name');

            return response()->json([
                'hide students details' => $stdHidden,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function onlyExample()
    {
        try {
            $student = Student::all();

            $only = $student->only([1, 3, 5, 7]);

            return response()->json([
                'students' => $only,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function setVisibleExample()
    {
        try {
            $student = Student::all();

            $visible = $student->setVisible(['id', 'first_name', 'email']);

            return response()->json([
                'studentDetails' => $visible,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function setHiddenExample()
    {
        try {
            $student = Student::all();

            $hidden = $student->setHidden(['first_name', 'last_name']);

            return response()->json([
                'studentDetails' => $hidden,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function toQueryExample()
    {
        try {
            $student = Student::where('last_name', 'Bharvad')->get();

            $student->toQuery()->update(['is_active' => 'false']);

            $updated = Student::where('last_name', 'Bharvad')->get(['first_name', 'last_name', 'email', 'is_active']);

            return response()->json([
                'updated_records' => $updated,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function transformExample()
    {
        try {
            $collection = collect([1, 2, 3, 4, 5]);

            $collection->transform(function ($item) {
                return $item * 2;
            });

            return response()->json([
                'transformed details' => $collection,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function undotExample()
    {
        try {
            $person = collect([
                'name.first_name' => 'John',
                'name.last_name' => 'Doe',
                'address.line_1' => '2992 Eagle Drive',
                'address.line_2' => '',
                'address.suburb' => 'Detroit',
                'address.state' => 'MI',
                'address.postcode' => '48219',
            ]);

            $undot = $person->undot();

            return response()->json([
                'undoted details' => $undot,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function timesExample()
    {
        try {
            $collection = Collection::times(10, function ($number) {
                return $number * 9;
            });

            return response()->json([
                'times example output' => $collection,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function zipExample()
    {
        try {
            $collection1 = collect([1, 2, 3]);

            $collection2 = collect([4, 5, 6]);

            $zipped = $collection1->zip($collection2);

            return response()->json([
                'zipped example value' => $zipped,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function unlessExample()
    {
        try {
            $collection = collect([1, 2, 3]);

            $unless = $collection->unless(true, function (Colection $collection) {
                return $collection->push(4);
            });

            return response()->json([
                'Details' => $unless,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function whenExample()
    {
        try {
            $collection = collect([1, 2, 3]);

            $when = $collection->when(true, function ($collection) {
                return $collection->push(4);
            });

            return response()->json([
                'Details' => $when,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function whenEmptyExample()
    {
        try {
            $collection = collect(['John', 'Jane']);

            $whenEmpty = $collection->whenEmpty(function ($collection) {
                return $collection->push('Adam');
            }, function ($collection) {
                return $collection->push('Taylor');
            });

            return response()->json([
                'Value insert when collection is empty' => $whenEmpty,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function manytomanyExample() {
        try {
            $student = Student::find(1);

            foreach ($student->courses as $course) {
                echo $course->pivot->created_at;
            }

            return response()->json([
                'course created time' => $course,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function studentCourseMail($studentId) {
        try {
            $student = Student::with('courses')->findOrFail($studentId);

            Mail::to($student->email)->send(new StudentCoursesDetails($student));

            return response()->json([
                'message' => 'student course details mail send successfully.'
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
