import { Component,OnInit,ViewChild} from '@angular/core';
import { NewAsoComponent } from './new-aso/new-aso.component';
import { MatTableDataSource } from "@angular/material/table";
import { MatSnackBar } from "@angular/material/snack-bar";
import { MatPaginator } from "@angular/material/paginator";
import { MatDialog } from "@angular/material/dialog";
import {ConsultasService} from "../../../services/consulta/consultas.service"
@Component({
  selector: 'app-aso',
  templateUrl: './aso.component.html',
  styleUrls: ['./aso.component.css']
})
export class ASOComponent implements OnInit {

  dataSource: MatTableDataSource<any>;
  dataInput: string;
  @ViewChild(MatPaginator, { static: true }) paginator: MatPaginator;
  displayedColumns: string[] = ['codConsulta', 'nome_paciente', 'nome_empresa', 'tipo_consulta', 'dataHora', 'validade', 'operations'];
  constructor(
    private dialog: MatDialog,
    public consultaService:ConsultasService,
    private _snackBar: MatSnackBar,
    
  ) { }
  ngOnInit() {
    this.carregarDadosTabela();
  }
 
  visualizar(codConsulta) {
    let dialog = this.dialog.open(NewAsoComponent, {
        width: '950px',
        autoFocus: false,
        maxHeight: '95vh',
        data: { codConsulta:codConsulta },
    })}
    carregarDadosTabela() {
      this.consultaService.listaDeConsultas().subscribe(consultas => {
        this.dataSource = new MatTableDataSource(consultas);
        this.dataSource.paginator = this.paginator;
      });
    }
  
    applyFilter(filterValue: string) {
      this.dataSource.filter = filterValue.trim().toLowerCase();
      if (this.dataSource.paginator) {
        this.dataSource.paginator.firstPage();
      }
    }
}

