<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Resource::create(['name' => 'Bares']);
        $company->permissions()->create(['name' => 'visualizar_bares']);
        $company->permissions()->create(['name' => 'visualizar_bar']);
        $company->permissions()->create(['name' => 'editar_bar']);
        $company->permissions()->create(['name' => 'apagar_bar']);

        $category = Resource::create(['name' => 'Categorias']);
        $category->permissions()->create(['name' => 'visualizar_categorias']);
        $category->permissions()->create(['name' => 'visualizar_categoria']);
        $category->permissions()->create(['name' => 'editar_categoria']);
        $category->permissions()->create(['name' => 'apagar_categoria']);

        $product = Resource::create(['name' => 'Produtos']);
        $product->permissions()->create(['name' => 'visualizar_produtos']);
        $product->permissions()->create(['name' => 'visualizar_produto']);
        $product->permissions()->create(['name' => 'editar_produto']);
        $product->permissions()->create(['name' => 'apagar_produto']);

        $favorite = Resource::create(['name' => 'Favoritos']);
        $favorite->permissions()->create(['name' => 'visualizar_favoritos']);
        $favorite->permissions()->create(['name' => 'visualizar_favorito']);
        $favorite->permissions()->create(['name' => 'editar_favorito']);
        $favorite->permissions()->create(['name' => 'apagar_favorito']);

        $order = Resource::create(['name' => 'Ordens']);
        $order->permissions()->create(['name' => 'visualizar_ordens']);
        $order->permissions()->create(['name' => 'visualizar_ordem']);
        $order->permissions()->create(['name' => 'editar_ordem']);
        $order->permissions()->create(['name' => 'apagar_ordem']);

        $payment = Resource::create(['name' => 'Pagamentos']);
        $payment->permissions()->create(['name' => 'listar_bandeiras']);
        $payment->permissions()->create(['name' => 'salvar_cartao']);
        $payment->permissions()->create(['name' => 'recuperar_cartao']);
        $payment->permissions()->create(['name' => 'processar_pagamento']);

        $admin = Resource::create(['name' => 'Admins']);
        $admin->permissions()->create(['name' => 'users']);
        $admin->permissions()->create(['name' => 'add_permissions_user']);
        $admin->permissions()->create(['name' => 'del_permissions_user']);

        $admin = Resource::create(['name' => 'PDVs']);
        $admin->permissions()->create(['name' => 'operar_pdv']);
    }
}
