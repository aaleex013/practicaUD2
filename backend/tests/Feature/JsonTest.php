<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JsonTest extends TestCase
{
    public function test_index_returns_valid_json_files()
     {
    Storage::fake('local');

    // Guardar archivos de prueba
    Storage::put('valid.json', json_encode(['key' => 'value']));
    Storage::put('invalid.txt', 'This is not a JSON file');

    // Realizar la petición GET a la ruta /api/json generada por apiResource
    $response = $this->get('/api/json');

    // Verificar el estado y el contenido de la respuesta
    $response->assertStatus(200)
             ->assertJson([
                 'mensaje' => 'Operación exitosa',
                 'contenido' => ['valid.json'],
             ]);
}

    public function test_store_creates_new_json_file()
    {
        Storage::fake('local');

        $data = [
            'filename' => 'newfile.json',
            'content' => json_encode(['key' => 'value'])
        ];

        $response = $this->post('/api/json', $data);

        $response->assertStatus(200)
                 ->assertJson(['mensaje' => 'Fichero guardado exitosamente']);


        Storage::disk('local')->assertExists('newfile.json');

    }

    public function test_store_returns_409_if_file_exists()
    {
        Storage::fake('local');

        Storage::put('existingfile.json', json_encode(['key' => 'value']));

        $data = [
            'filename' => 'existingfile.json',
            'content' => json_encode(['key' => 'value'])
        ];

        $response = $this->post('/api/json', $data);

        $response->assertStatus(409)
                 ->assertJson(['mensaje' => 'El fichero ya existe']);
    }

    public function test_store_returns_415_if_content_is_invalid_json()
    {
        Storage::fake('local');

        $data = [
            'filename' => 'invalidfile.json',
            'content' => 'This is not a JSON'
        ];

        $response = $this->post('/api/json', $data);

        $response->assertStatus(415)
                 ->assertJson(['mensaje' => 'Contenido no es un JSON válido']);
    }

    public function test_show_returns_file_content()
    {
        Storage::fake('local');

        Storage::put('existingfile.json', json_encode(['key' => 'value']));


        $response = $this->get('/api/json/existingfile.json');

        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => 'Operación exitosa',
                     'contenido' => ['key' => 'value']
                 ]);
    }

    public function test_show_returns_404_if_file_not_exists()
    {
        Storage::fake('local');

        $response = $this->get('/api/json/nonexistentfile.json');

        $response->assertStatus(404)
                 ->assertJson(['mensaje' => 'El fichero no existe']);
    }

    public function test_update_modifies_existing_file()
    {
        Storage::fake('local');

        Storage::put('existingfile.json', json_encode(['key' => 'value']));

        $data = [

            'content' => json_encode(['new_key' => 'new_value'])
        ];

        $response = $this->put('/api/json/existingfile.json', $data);

        $response->assertStatus(200)
                 ->assertJson(['mensaje' => 'Fichero actualizado exitosamente']);

        Storage::assertExists('existingfile.json');
        $this->assertEquals(json_encode(['new_key' => 'new_value']), Storage::get('existingfile.json'));
    }

    public function test_update_returns_404_if_file_not_exists()
    {
        Storage::fake('local');

        $data = [
            'filename' => 'nonexistentfile.json',
            'content' => json_encode(['key' => 'value'])
        ];

        $response = $this->put('/api/json/nonexistentfile.json', $data);

        $response->assertStatus(404)
                 ->assertJson(['mensaje' => 'El fichero no existe']);
    }

    public function test_update_returns_415_if_content_is_invalid_json()
    {
        Storage::fake('local');

        Storage::put('existingfile.json', json_encode(['key' => 'value']));

        $data = [
            'filename' => 'existingfile.json',
            'content' => 'This is not a JSON'
        ];

        $response = $this->put('/api/json/existingfile.json', $data);

        $response->assertStatus(415)
                 ->assertJson(['mensaje' => 'Contenido no es un JSON válido']);
    }

    public function test_destroy_deletes_existing_file()
    {
        Storage::fake('local');

        Storage::put('existingfile.json', json_encode(['key' => 'value']));

        $response = $this->delete('/api/json/existingfile.json');

        $response->assertStatus(200)
                 ->assertJson(['mensaje' => 'Fichero eliminado exitosamente']);

        Storage::assertMissing('existingfile.json');
    }

    public function test_destroy_returns_404_if_file_not_exists()
    {
        Storage::fake('local');

        $response = $this->delete('/api/json/nonexistentfile.json');

        $response->assertStatus(404)
                 ->assertJson(['mensaje' => 'El fichero no existe']);
    }
}
