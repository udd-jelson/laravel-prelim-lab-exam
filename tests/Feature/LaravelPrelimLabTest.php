<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LaravelPrelimLabTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function home_about_and_contact_pages_are_accessible(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Student Record');

        $this->get('/about')
            ->assertStatus(200);

        $this->get('/contact')
            ->assertStatus(200);
    }

    #[Test]
    public function navigation_links_are_visible_on_home_page(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Home')
            ->assertSee('About')
            ->assertSee('Contact')
            ->assertSee('Students')
            ->assertSee('Add Student');
    }

    #[Test]
    public function students_table_exists_with_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('students'));

        $this->assertTrue(Schema::hasColumns('students', [
            'id',
            'student_number',
            'first_name',
            'last_name',
            'course',
            'year_level',
            'created_at',
            'updated_at',
        ]));
    }

    #[Test]
    public function student_model_has_correct_fillable_fields(): void
    {
        $student = new Student();

        $this->assertEquals([
            'student_number',
            'first_name',
            'last_name',
            'course',
            'year_level',
        ], $student->getFillable());
    }

    #[Test]
    public function student_seeder_inserts_at_least_three_records(): void
    {
        $this->seed(\Database\Seeders\StudentSeeder::class);

        $this->assertGreaterThanOrEqual(3, Student::count());
    }

    #[Test]
    public function students_page_displays_database_records(): void
    {
        Student::create([
            'student_number' => '2026-0001',
            'first_name' => 'Ana',
            'last_name' => 'Santos',
            'course' => 'BSIT',
            'year_level' => '1st Year',
        ]);

        Student::create([
            'student_number' => '2026-0002',
            'first_name' => 'Ben',
            'last_name' => 'Reyes',
            'course' => 'BSCS',
            'year_level' => '2nd Year',
        ]);

        $this->get('/students')
            ->assertStatus(200)
            ->assertSee('2026-0001')
            ->assertSee('Ana')
            ->assertSee('Santos')
            ->assertSee('BSIT')
            ->assertSee('2026-0002')
            ->assertSee('Ben')
            ->assertSee('Reyes')
            ->assertSee('BSCS');
    }

    #[Test]
    public function add_student_form_saves_a_new_student_record(): void
    {
        $this->get('/students/create')
            ->assertStatus(200)
            ->assertSee('student_number')
            ->assertSee('first_name')
            ->assertSee('last_name')
            ->assertSee('course')
            ->assertSee('year_level');

        $response = $this->post('/students', [
            'student_number' => '2026-0099',
            'first_name' => 'Carlo',
            'last_name' => 'Dela Cruz',
            'course' => 'BSIT',
            'year_level' => '3rd Year',
        ]);

        $response->assertRedirect('/students');

        $this->assertDatabaseHas('students', [
            'student_number' => '2026-0099',
            'first_name' => 'Carlo',
            'last_name' => 'Dela Cruz',
            'course' => 'BSIT',
            'year_level' => '3rd Year',
        ]);
    }
}
