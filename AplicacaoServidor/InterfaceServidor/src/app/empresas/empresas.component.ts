import { Component, OnInit } from '@angular/core';
import { SidenavComponent } from '../sidenav/sidenav.component';

import { EmpresaService } from '../../services/empresa.service';
@Component({
	selector: 'app-empresas',
	templateUrl: './empresas.component.html',
	styleUrls: ['./empresas.component.css']
})
export class EmpresasComponent implements OnInit {

	constructor(public sideNav:SidenavComponent, private empresaService:EmpresaService) { }

	empresas:any;
	html:string;

	async ngOnInit() {
		this.sideNav.activeView="Empresas";
		this.empresas = await this.empresaService.listaDeEmpresas();
		this.html = this.popularTabela();
	}
	
	popularTabela(){
		let i = 0;
		var html = this.empresas.map( empresa => {
			return '<tr data-codigo='+ empresa.codEmpresa +'><th scope="row"> '+ (++i) +' </th><td> '+ empresa.nome +' </td><td> '+ empresa.cnpj +' </td><td> '+ empresa.telefone +' </td><td> '+ (empresa.tipoPgto == 1 ? 'Fatura' : 'À vista' ) +' </td></tr>'
		}).join('');
		
		return html;
	}

	buscaTermo(event){
		const regex = new RegExp(event.target.value, 'gi');
		
		var busca = this.empresas.filter( empresa => {
			return empresa.nome.match(regex) || empresa.cnpj.match(regex) || empresa.telefone.match(regex);
		});
		this.mostraBusca(busca);
	}

	mostraBusca(resultadoBusca){
		let i =0;
		this.html = resultadoBusca.map( empresa => {
			return '<tr data-codigo='+ empresa.codEmpresa +'><th scope="row"> '+ (++i) +' </th><td> '+ empresa.nome +' </td><td> '+ empresa.cnpj +' </td><td> '+ empresa.telefone +' </td><td> '+ (empresa.tipoPgto == 1 ? 'Fatura' : 'À vista' ) +' </td></tr>'
		}).join('');
	}
}
