import { Component, OnInit, ViewChild } from '@angular/core';
import { SidenavComponent } from '../sidenav/sidenav.component';

import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';

import { PacienteService } from '../../services/paciente/paciente.service';
import { paciente } from '../../services/paciente/paciente';
import { ModalPacientesComponent } from './modal-pacientes/modal-pacientes.component';

@Component({
    selector: 'app-pacientes',
    templateUrl: './pacientes.component.html'
})
export class PacientesComponent implements OnInit {

    displayedColumns: string[] = ['name', 'cpf', 'operations'];
    dataSource: MatTableDataSource<paciente>;
    dataInput: string;

    @ViewChild(MatPaginator, { static: true }) paginator: MatPaginator;

    constructor(public sideNav: SidenavComponent, private pacienteService: PacienteService,    private dialog: MatDialog,) { }

    ngOnInit() {
        this.sideNav.activeView = "Pacientes";
        this.carregarDadosTabela();
    }

    async carregarDadosTabela() {
        await this.pacienteService.listaDePacientes().subscribe(pacientes => {
            this.dataSource = new MatTableDataSource(pacientes);
            this.dataSource.paginator = this.paginator;
        });

    }
    visualizar(id) {
        let dialog = this.dialog.open(ModalPacientesComponent, {
            width: '700px', data: { id: id, acao: 'VISUALIZAR' }
        })
        dialog.afterClosed().subscribe(() => {
            this.ngOnInit();
        });
    }

    editar(id) {
        let dialog = this.dialog.open(ModalPacientesComponent, {
            width: '700px', data: { id: id, acao: 'EDITAR' }
        })
        dialog.afterClosed().subscribe(() => {
            this.ngOnInit();
        })
    }
    deletar(id) {
        let dialog = this.dialog.open(ModalPacientesComponent, {
            width: '400px', data: { id: id, acao: 'DELETAR' }
        })
        dialog.afterClosed().subscribe(() => {
            this.ngOnInit();
        })
    }
    applyFilter(filterValue: string) {
        this.dataSource.filter = filterValue.trim().toLowerCase();
        if (this.dataSource.paginator) {
            this.dataSource.paginator.firstPage();
        }
    }

}
