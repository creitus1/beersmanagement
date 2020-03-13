<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
    </ol>
    <div class="container-fluid">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> Tipos de pintura
          <button
            type="button"
            @click="abrirModal('tipo','registrar')"
            class="btn btn-secondary"
          >
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <div class="col-md-6">
              <div class="input-group">
                <select class="form-control col-md-3" v-model="criterio">
                  <option value="nombre">Nombre</option>
                  <option value="descripcion">Descripción</option>
                </select>
                <input
                  type="text"
                  v-model="buscar" @keyup.enter="listarTipo(1, buscar, criterio)"
                  class="form-control"
                  placeholder="Texto a buscar"
                />
                <button type="submit" @click="listarTipo(1, buscar, criterio)" class="btn btn-primary">
                  <i class="fa fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </div>
          <table class="table table-bordered table-striped table-sm">
            <thead>
              <tr>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tipo in arrayTipo" :key="tipo.id">
                <td>
                  <button
                    type="button"
                    @click="abrirModal('tipo','actualizar',tipo)"
                    class="btn btn-warning btn-sm"
                  >
                    <i class="icon-pencil"></i>
                  </button> &nbsp;
                  <template v-if="tipo.condicion">
                    <button
                      type="button"
                      class="btn btn-danger btn-sm"
                      @click="desactivarTipo(tipo.id)"
                    >
                      <i class="icon-trash"></i>
                    </button>
                  </template>
                  <template v-else>
                    <button
                      type="button"
                      class="btn btn-info btn-sm"
                      @click="activarTipo(tipo.id)"
                    >
                      <i class="icon-check"></i>
                    </button>
                  </template>
                </td>
                <td v-text="tipo.nombre"></td>
                <td v-text="tipo.descripcion"></td>
                <td>
                  <div v-if="tipo.condicion">
                    <span class="badge badge-success">Activo</span>
                  </div>
                  <div v-else>
                    <span class="badge badge-danger">Desactivado</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <nav>
            <ul class="pagination">
              <li class="page-item" v-if="pagination.current_page > 1">
                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page -1, buscar, criterio)">Ant</a>
              </li>
              <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active': '']" >
                <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio)" v-text="page"></a>
              </li>
              <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio)">Sig</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
    <!--Inicio del modal agregar/actualizar-->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{'mostrar' : modal}"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" v-text="tituloModal"></h4>
            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <form action method="post" enctype="multipart/form-data" class="form-horizontal">
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                <div class="col-md-9">
                  <input
                    type="text"
                    v-model="nombre"
                    class="form-control"
                    placeholder="Nombre del tipo de pintura"
                  />
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="email-input">Descripción</label>
                <div class="col-md-9">
                  <input
                    type="text"
                    v-model="descripcion"
                    class="form-control"
                    placeholder="Descripción"
                  />
                </div>
              </div>
              <div v-show="errorTipo" class="form-group-row div-error">
                <div class="text-center text-error">
                  <div v-for="error in errorMostrarMsjTipo" :key="error" v-text="error"></div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
            <button
              type="button"
              v-if="tipoAccion==1"
              class="btn btn-primary"
              @click="registrarTipo()"
            >Guardar</button>
            <button
              type="button"
              v-if="tipoAccion==2"
              class="btn btn-primary"
              @click="actualizarTipo()"
            >Actualizar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
    <!-- Inicio del modal Eliminar -->

    <!-- Fin del modal Eliminar -->
  </main>
</template>

<script>
export default {
  data() {
    return {
      tipo_id: 0,
      nombre: "",
      descripcion: "",
      arrayTipo: [],
      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorTipo: 0,
      errorMostrarMsjTipo: [],
      pagination: {
        'total': 0,
        'current_page': 0,
        'per_page': 0,
        'last_page': 0,
        'from': 0,
        'to': 0,
      },
      offset: 3,
      criterio :'nombre',
      buscar :''
    }
  },
  computed: {
    isActived: function() {
      return this.pagination.current_page;
    },
    //calcula los elementos de la paginación
    pagesNumber: function() {
      if (!this.pagination.to) {
        return [];
      }

      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      var to = from + (this.offset * 2);
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }
      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    }
  },
  methods: {
    listarTipo(page,buscar,criterio) {
      let me = this;
      var url = '/tipo?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
      axios.get(url).then(function(response) {
          var respuesta = response.data;
          me.arrayTipo = respuesta.tipos.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    cambiarPagina(page, buscar, criterio) {
      let me = this;
      //actualizar la pagina actual
      me.pagination.current_page = page;
      //enviar la petición para visualizar la data de esa página
      me.listarTipo(page, buscar, criterio);
    },
    registrarTipo() {
      if (this.validarTipo()) {
        return;
      }
      let me = this;
      axios
        .post("/tipo/registrar", {
          nombre: this.nombre,
          descripcion: this.descripcion
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarTipo(1,'','nombre');
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    actualizarTipo() {
      if (this.validarTipo()) {
        return;
      }
      let me = this;
      axios
        .put("tipo/actualizar", {
          nombre: this.nombre,
          descripcion: this.descripcion,
          id: this.tipo_id
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarTipo(1,'','nombre');
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    desactivarTipo(id){
               swal({
                title: 'Esta seguro de desactivar este tipo de pintura?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put('/tipo/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarTipo(1,'','nombre');
                        swal(
                        'Desactivado!',
                        'El registro ha sido desactivado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            },
            activarTipo(id){
               swal({
                title: 'Esta seguro de activar este tipo de pintura?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put('/tipo/activar',{
                        'id': id
                    }).then(function (response) {
                        me.listarTipo(1,'','nombre');
                        swal(
                        'Activado!',
                        'El registro ha sido activado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            },
    validarTipo() {
      this.errorTipo = 0;
      this.errorMostrarMsjTipo = [];
      if (!this.nombre)
        this.errorMostrarMsjTipo.push(
          "El nombre del tipo de pintura no puede estar vacio."
        );
      if (this.errorMostrarMsjTipo.length) this.errorTipo = 1;
      return this.errorTipo;
    },
    cerrarModal() {
      (this.modal = 0),
        (this.tituloModal = ""),
        (this.nombre = ""),
        (this.descripcion = "");
    },
    abrirModal(modelo, accion, data = []) {
      switch (modelo) {
        case "tipo": {
          switch (accion) {
            case "registrar": {
              (this.modal = 1),
                (this.tituloModal = "Registrar tipo de pintura"),
                (this.nombre = ""),
                (this.descripcion = ""),
                (this.tipoAccion = 1);
              break;
            }

            case "actualizar": {
              //console.log(data);
              this.modal = 1;
              this.tituloModal = "actualizar tipo de pintura";
              this.tipoAccion = 2;
              this.tipo_id = data["id"];
              this.nombre = data["nombre"];
              this.descripcion = data["descripcion"];
              break;
            }
          }
        }
      }
    }
  },
  mounted() {
    this.listarTipo(1,this.buscar,this.criterio);
  }
};
</script>
<style >
.modal-content {
  width: 100% !important;
  position: absolute !important;
}
.mostrar {
  display: list-item !important;
  opacity: 1 !important;
  position: absolute !important;
  background-color: #3c29297a !important;
}
.div-error {
  color: red !important;
  font-weight: bold;
}
</style>
