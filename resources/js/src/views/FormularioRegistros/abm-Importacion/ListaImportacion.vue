   
<template>
    <section>
        <div>
            <!-- Formulario Modal -->
            <b-modal ref="frm-importacion" id="frm-importacion" ok-title="Cerrar" ok-variant="danger" ok-only size="xl"
                centered title="Registro de Importación" no-close-on-backdrop @ok="listaImportacion">
                <!-- Diseño del Formulario -->
                <Frm_Importacion></Frm_Importacion>
            </b-modal>
        </div>
        <b-row>
            <b-col md="9">
                <b-overlay id="overlay-background" :variant="variant" :opacity="opacity" :blur="blur">
                    <b-card border-variant="info">
                        <b-card-body>
                            <b-row>
                                <b-col>

                                    <b-row class="justify-content-between">
                                        <b-col sm="12" md="4" xl="6" lg="6" class="mb-1">
                                            <!-- Boton Modal -->
                                            <b-button v-ripple.400="'rgba(113, 102, 240, 0.15)'" v-b-modal.frm-importacion
                                                variant="success" @click="clickAccion('', 'guardar')"
                                                :class="{ 'd-none': $store.state.app.isCrea }">
                                                Nuevo Registro
                                            </b-button>
                                        </b-col>
                                        <b-col sm="12" md="8" xl="6" lg="6">
                                            <b-form-group label-for="filter-input">
                                                <b-input-group>
                                                    <b-form-input id="filter-input" v-model="filter" debounce="200"
                                                        type="search" placeholder="Esccribe Para Buscar"></b-form-input>
                                                    <b-input-group-append>
                                                        <b-button :disabled="!filter" variant="danger"
                                                            @click="filter = ''">Limpiar</b-button>
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <!-- Tabla --> <!-- Listado -->
                                            <b-table id="tabla-lista-ventas" v-model="items" :items="items" :fields="fields"
                                                :filter="filter" @filtered="onFiltered" hover :bordered="true"
                                                :busy="isBusy" outlined stacked="sm" small :style="{ fontSize: fontSize }"
                                                :sticky-header="stickyHeader">
                                                <template #cell(impEstadoImportacion)="data">
                                                    <b-badge :variant="data.item.BBadge">
                                                        <div class=" d-flex align-items-center">
                                                            <span>{{ data.item.impEstadoImportacion }}</span>
                                                        </div>
                                                    </b-badge>
                                                </template>
                                                <template #cell(Acción)="row">
                                                    <b-row>

                                                        <b-col md="4">
                                                            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                                                variant="flat-success" v-b-tooltip.hover.v-dark
                                                                title="Ver Detalle" class="btn-icon"
                                                                :class="{ 'd-none': $store.state.app.isVer }"
                                                                @click="clickAccion(row.item, ('ver'))">
                                                                <feather-icon icon="EyeIcon" />
                                                            </b-button>
                                                        </b-col>
                                                        <b-col md="4">
                                                            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                                                variant="flat-danger" class="btn-icon rounded-circle"
                                                                :class="{ 'd-none': $store.state.app.isElimina }"
                                                                @click="clickAccion(row.item, ('eliminar'))">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </b-col>
                                                        <b-col md="4">
                                                            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                                                variant="flat-info" class="btn-icon rounded-circle"
                                                                :class="{ 'd-none': $store.state.app.isGasto }"
                                                                @click="clickAccion(row.item, ('guardarGI'))">
                                                                <feather-icon icon="CreditCardIcon" />
                                                            </b-button>
                                                        </b-col>
                                                    </b-row>

                                                </template>
                                                <template #table-busy>
                                                    <div class="text-center text-danger my-2">
                                                        <b-spinner class="align-middle"></b-spinner>
                                                        <strong>Cargando...</strong>
                                                    </div>
                                                </template>
                                            </b-table>
                                            <b-pagination v-model="currentPage" :per-page="perPage" :total-rows="totalRows"
                                                size="lg" class="mt-2" align="center">
                                                <template #first-text>
                                                    <span class="text-success">Primero</span>
                                                </template>
                                                <template #prev-text>
                                                    <span class="text-warning">Anterior</span>
                                                </template>
                                                <template #next-text>
                                                    <span class="text-info">Próximo</span>
                                                </template>
                                                <template #last-text>
                                                    <span class="text-danger">Ultimo</span>
                                                </template>
                                                <template #ellipsis-text>
                                                    <div>
                                                        <b-spinner small type="grow" />
                                                        <b-spinner small type="grow" />
                                                    </div>
                                                </template>
                                                <template #page="{ page, active }">
                                                    <b v-if="active">{{ page }}</b>
                                                    <i v-else>{{ page }}</i>
                                                </template>
                                            </b-pagination>
                                        </b-col>
                                    </b-row>
                                </b-col>
                            </b-row>

                        </b-card-body>
                    </b-card>
                </b-overlay>
            </b-col>
            <b-col md="3">
                <b-row>
                    <b-col>
                        <b-card border-variant="info">
                            <b-card-body>
                                Ventas Contado :{{ totalVentaSum }}
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <b-row class="align-items-center">
                            <b-col md="9">
                                <h5 class="font-weight-bold">
                                    Fecha Reporte
                                </h5>
                            </b-col>
                            <b-col md="3" class="d-flex justify-content-end">
                                <!-- Boton de Imprimir PDF -->
                                <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" pill variant="outline-dark"
                                    class="btn-icon" @click="generarPDF()">
                                    <feather-icon icon="PrinterIcon" />
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <b-form-group>

                            <flat-pickr v-model="dateDefault" class="form-control" />
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <div id="pdfIframeContainer">
                            <iframe id="pdfIframe" width="100%" height="300"></iframe>
                        </div>
                    </b-col>

                </b-row>


            </b-col>

        </b-row>

    </section>
</template>
<script>
import {
    BButtonToolbar,
    BButtonGroup,
    VBTooltip,
    BFormFile,
    BFormDatepicker,
    BRow,
    BModal,
    VBModal,
    BAvatar,
    BCardTitle,
    BCardBody,
    BCardHeader,
    BCard,
    BDropdown,
    BDropdownItem,
    BButton,
    BFormSelect,
    BCol,
    BFormGroup,
    BFormInput,
    BFormCheckbox,
    BForm,
    BFormText,
    BFormDatalist,
    BBadge,
    BTable,
    BMedia,
    BFormTextarea,
    BInputGroupAppend,
    BInputGroup,
    BOverlay,
    BSpinner,
    BFormValidFeedback,
    BFormInvalidFeedback,
    BPagination
} from "bootstrap-vue";
import flatPickr from 'vue-flatpickr-component'
// import Ripple from "vue-ripple-directive";
import Ripple from "vue-ripple-directive";
import vSelect from 'vue-select'


import jsPDF from 'jspdf';
import 'jspdf-autotable';
import Frm_Importacion from './frm_Importacion/frm_Importacion.vue';


//   import Frm_Producto from "./frm_Producto/frm_Producto.vue";

export default {
    components: {
        BPagination,
        flatPickr,
        BButtonToolbar,
        BButtonGroup,
        VBTooltip,
        vSelect,
        BFormFile,
        BFormValidFeedback,
        BFormInvalidFeedback,
        BOverlay,
        BFormDatepicker,
        BInputGroupAppend,
        BInputGroup,
        BRow,
        BModal,
        VBModal,
        BTable,
        BAvatar,
        BCardTitle,
        BCardBody,
        BCardHeader,
        BCard,
        BDropdown,
        BDropdownItem,
        BButton,
        BFormSelect,
        BFormTextarea,
        BCol,
        BFormGroup,
        BFormInput,
        BFormCheckbox,
        BForm,
        BMedia,
        BFormText,
        BFormDatalist,
        BBadge,
        BSpinner,
        Frm_Importacion,

    },
    directive: {
        "b-tooltip": VBTooltip,
        Ripple
    },
    data() {
        return {
            currentPage: 1,
            perPage: 5,  // Número de elementos por página
            totalRows: 1,
            nVenta: 0,
            cliente: "",
            fontSize: "",
            dateDefault: null,
            txtCantidad: 0,
            TipoAccion: null,
            stickyHeader: true,
            transProps: {
                // Transition name
                name: "flip-list",
            },
            txtCodigoInterno: 0,
            totalVentas: 0,
            shows: true,
            isBusy: false,
            // totalRows: 1,
            loaded: false,
            filter: "",
            filterOn: [],
            fecha: 0,
            fechaRegistro: "",
            Loading: "",
            estado: "",
            items: [],
            fields: [

                { key: "impNumero", label: "Nª", sortable: true },
                { key: "provNombre", label: "Proveedor", sortable: true },
                { key: "almNombreAlmacen", label: "ALM. Destino", sortable: true },
                { key: "impDescripcion", label: "Descripcion", sortable: true },
                { key: "impFechaElaboracion", label: "Fecha Elaboración", sortable: true },
                { key: "impEstadoImportacion", label: "Estado", sortable: true },

                { key: "Acción", sortable: false },
            ],
            itemDetalle: [],
            show: false,
            variant: "dark",
            opacity: 0.85,
            blur: "2px",
            // selected: [],
        };
    },

    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter((f) => f.sortable)
                .map((f) => {
                    return { text: f.label, value: f.key };
                });
        },
        totalVentaSum() {
            return this.items.reduce((total, venta) => total + parseFloat(venta.totalVenta), 0).toFixed(2);
        },
        totalRows() {
            this.totalRows = this.items.length
        },
    },
    watch: {

        currentPage: function (newPage) {
            this.currentPage = newPage
            this.listaImportacion()
        },
        dateDefault: function (date) {
            const dateA = date
            this.obtenerVentasPorFecha(this.dateDefault)
            // this.generarPDF(this.items)
        },
    },
    mounted() {
        // this.VerificarAperturaCaja()
        this.listaImportacion()
        // this.clickAccion('', "apertura")
        this.sizeTablas()
    },
    methods: {



        async detalleVenta(vntNumero) {

            try {
                let me = this;
                const lista = [];
                me.itemDetalle = []
                const formData = new FormData();
                formData.append("vntNumero", vntNumero);
                const response = await this.$http.post("detalleVenta", formData);
                const resp = response.data;
                for (let i = 0; i < resp.length; i++) {
                    lista.push({
                        "nombre": resp[i].nombre,
                        "app": resp[i].app,
                        "apm": resp[i].apm,
                        "vntFechaCreacion": resp[i].vntFechaCreacion,
                        "vntNumero": resp[i].vntNumero,
                        "artNombre": resp[i].artNombre,
                        "vndCantidad": resp[i].vndCantidad,
                        "vndPrecioVenta": resp[i].vndPrecioVenta,
                        "vndDescuento": resp[i].vndDescuento,
                        "subtotal": resp[i].subtotal,
                    });
                }
                me.itemDetalle = lista;

                this.generatePDF_Detalle(this.itemDetalle);
            } catch (error) {
                console.log(error.message);
                this.UsuarioAlerta("error", error.response);
                this.showOverlay = false;
            }
        },
        generatePDF_Detalle(Articulo) {
            try {

                const doc = new jsPDF();
                let me = this;

                // Agregar el logo de la empresa (reemplaza 'ruta_al_logo' con la ruta de tu imagen)
                const image = new Image();
                var imgData = 'data:image/png;base64,' + me.$store.state.app.LogoEmpresa;
                doc.addImage(imgData, 'PNG', 15, 5, 25, 25);
                doc.setFont('helvetica', 'neue')
                doc.setFontSize(8);
                // doc.text(me.$store.state.app.NombreEmpresa, 40, 20);
                doc.text('DIRECCION : ' + me.$store.state.app.DireccionEmpresa, 40, 20);
                doc.text('TELEFONO  : ' + me.$store.state.app.TelefonoEmpresa, 40, 25);
                doc.text('Nit  : ' + me.$store.state.app.NitEmpresa, 40, 30);
                const currentDate = new Date(); // Obtiene la fecha actual
                const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
                const formattedDate = Articulo[0]["vntFechaCreacion"] //currentDate.toLocaleDateString('es-ES', options); // Formatea la fecha como "10/11/2023"
                me.cliente = Articulo[0]["nombre"] + " " + Articulo[0]["app"]
                // Configuración de la nota de venta
                const notaDeVenta = {
                    numero: Articulo[0]["vntNumero"],
                    fecha: formattedDate,
                    cliente: me.cliente,

                };


                // Datos de los productos
                const columns = ['Articulo', 'Cantidad', 'PrecioUnitario', 'Descuento', 'SubTotal'];
                const rows = Articulo.map((producto) => {
                    const precioOriginal = parseFloat(producto.vndPrecioVenta);
                    const precioUnitario = producto.vndDescuento ? parseFloat(producto.vndDescuento) : precioOriginal;
                    const subtotal = precioUnitario * parseFloat(producto.vndCantidad);

                    return [
                        producto.artNombre || '',
                        producto.vndCantidad || 0,
                        precioOriginal.toFixed(2), // Mostrar el precio original en la columna 'Precio'
                        producto.vndDescuento || 0,
                        producto.subtotal
                    ];
                });

                // Agregar el encabezado de la nota de venta
                doc.setFontSize(22);
                doc.setFont('helvetica', 'neue');
                doc.text('Nota de Venta', 135, 10);
                doc.setFontSize(14);
                doc.setFont('helvetica', 'neue');

                doc.text('N°:', 135, 20);
                // doc.setTextColor(110, 107, 123);
                doc.text(`${notaDeVenta.numero}`, 145, 20);
                doc.setTextColor(0);
                doc.setFont('helvetica', 'neue');
                doc.text(`Fecha: ${notaDeVenta.fecha}`, 135, 28);
                doc.setFont('helvetica', 'neue');
                // doc.setTextColor(0);
                doc.text("Cliente:", 15, 52);
                // doc.setTextColor(100);
                doc.setFont('helvetica', 'neue');
                doc.text(`${notaDeVenta.cliente}`, 35, 52);
                const columnStyles = {
                    0: { halign: 'text-left' }, // Alineación centrada para la primera columna
                    1: { halign: 'center' }, // Alineación centrada para la segunda columna
                    2: { halign: 'center' }, // Alineación centrada para la tercera columna
                    3: { halign: 'center' }, // Alineación centrada para la cuarta columna
                    4: { halign: 'center' }, // Alineación centrada para la quinta columna
                };
                // Agregar la tabla de productos
                doc.autoTable({
                    startY: 60,
                    head: [columns],
                    body: rows,
                    columnStyles: columnStyles,
                });


                // Calcular el total
                // const total = Articulo.reduce((acc, producto) => acc + parseFloat(producto.precioV) * parseInt(producto.cantidad), 0);
                const total = Articulo.reduce((acc, producto) => {
                    const precioUnitario = producto.subtotal;
                    const subtotal = producto.subtotal++;
                    return acc + subtotal;
                }, 0);
                doc.setFont('helvetica', 'neue');
                doc.text(`Total Bs.:`, 145, doc.autoTable.previous.finalY + 10);
                doc.text(`${total.toFixed(2)}`, 175, doc.autoTable.previous.finalY + 10);

                doc.setFont('times', 'normal');
                doc.setFontSize(12);

                // Guardar el documento PDF como un Data URI
                const dataUri = doc.output('datauristring');

                // Abrir el PDF en una nueva ventana o pestaña
                const newWindow = window.open();
                newWindow.document.write('<iframe width="100%" height="100%" src="' + dataUri + '"></iframe');
            } catch (error) {
                me.UsuarioAlerta("error", "Error al generar el PDF: " + error.message);
            }
        }
        ,
        generarPDF() {

            const doc = new jsPDF();
            let me = this;

            // Agregar el logo de la empresa (reemplaza 'ruta_al_logo' con la ruta de tu imagen)
            const image = new Image();
            var imgData = 'data:image/png;base64,' + me.$store.state.app.LogoEmpresa;
            doc.addImage(imgData, 'PNG', 15, 5, 25, 25);
            doc.setFont('helvetica', 'neue')
            doc.setFontSize(8);
            // doc.text(me.$store.state.app.NombreEmpresa, 40, 20);
            doc.text('DIRECCION : ' + me.$store.state.app.DireccionEmpresa, 40, 20);
            doc.text('TELEFONO  : ' + me.$store.state.app.TelefonoEmpresa, 40, 25);
            doc.text('Nit  : ' + me.$store.state.app.NitEmpresa, 40, 30);
            const currentDate = new Date(); // Obtiene la fecha actual
            const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
            const formattedDate = currentDate.toLocaleDateString('es-ES', options); // Formatea la fecha como "10/11/2023"


            // Datos de los productos
            const columns = ['N°', 'Cliente', 'Fecha', 'TipoPago', 'SubTotal'];
            const rows = me.items.map((historial) => {

                return [
                    historial.vntNumero,
                    historial.cliNombre,
                    historial.vntFechaCreacion,
                    historial.FormaPago,
                    historial.totalVenta

                ]
            })

            // Agregar el encabezado de la nota de venta
            doc.setFontSize(22);
            doc.setFont('helvetica', 'neue');
            doc.text('Reporte de Venta', 135, 10);
            doc.setFontSize(14);
            doc.setFont('helvetica', 'neue');

            const columnStyles = {
                0: { halign: 'text-left' }, // Alineación centrada para la primera columna
                1: { halign: 'center' }, // Alineación centrada para la segunda columna
                2: { halign: 'center' }, // Alineación centrada para la tercera columna
                3: { halign: 'center' }, // Alineación centrada para la cuarta columna
                4: { halign: 'center' }, // Alineación centrada para la quinta columna
            };
            // Agregar la tabla de productos
            doc.autoTable({
                startY: 60,
                head: [columns],
                body: rows,
                columnStyles: columnStyles,
            });

            const total = me.items.reduce((acc, venta) => {

                const subtotal = acc + parseFloat(venta.totalVenta);
                return subtotal;
            }, 0);

            doc.setFont('helvetica', 'neue');
            doc.text(`Total Venta Bs.:`, 145, doc.autoTable.previous.finalY + 10);
            doc.text(`${total}`, 180, doc.autoTable.previous.finalY + 10);

            doc.setFont('times', 'normal');
            doc.setFontSize(12);
            // Guardar el documento PDF como un Data URI
            const dataUri = doc.output('datauristring');

            const iframeContainer = document.getElementById('pdfIframeContainer');
            const iframe = document.getElementById('pdfIframe');

            // ... (generación del PDF)

            // Establecer la fuente del iframe
            iframe.src = dataUri;

            // Asegurarse de que el contenedor esté visible
            iframeContainer.style.display = 'block';
            // Abrir el PDF en una nueva ventana
            // const newWindow = window.open();
            // newWindow.document.write('<iframe width="100%" height="100%" src="' + dataUri + '"></iframe');
        },
        sizeTablas() {
            const anchoVentana = window.innerWidth;

            if (anchoVentana <= 576) {
                // Dispositivo móvil pequeño
                this.fontSize = 'x-small';
            } else if (anchoVentana <= 768) {
                // Dispositivo móvil o tableta
                this.fontSize = 'small';
            } else if (anchoVentana <= 992) {
                // Tableta o dispositivo de tamaño medio
                this.fontSize = 'medium';
            } else {
                // Pantalla de escritorio
                this.fontSize = 'small';
            }
        },

        //Metodos Estandar
        clickAccion(item, accion) {


            if (accion === "apertura") {
                this.$store.dispatch('app/cambiarTipoAccion', { tipo: accion, variant: 'success', icono: 'SaveIcon', texto: 'Apertura Caja', Bclass: '' })
            }
            if (accion === "guardar") {
                this.$store.dispatch('app/cambiarTipoAccion', { tipo: accion, variant: 'success', icono: 'SaveIcon', texto: 'Guardar', Bclass: '' })
                
            }
            if (accion === "guardarGI") {
                this.$store.dispatch('app/cambiaId', item["impNumero"])
                this.$store.dispatch('app/cambiarTipoAccion', { tipo: accion, variant: 'success', icono: 'SaveIcon', texto: 'Guardar', Bclass: '' })
                this.$refs["frm-importacion"].show();
            }
            if (accion === "editar") {

                this.$store.dispatch('app/cambiaId', item["impNumero"])
                this.$store.dispatch('app/cambiarTipoAccion', { tipo: accion, variant: 'primary', icono: 'SaveIcon', texto: 'Modificar', Bclass: '' })
                this.$refs["frm-importacion"].show();
                // this.$refs["frm-articulo"].show();
            }
            if (accion === "ver") {
                this.$store.dispatch('app/cambiaId', item["impNumero"])
                this.$store.dispatch('app/cambiarTipoAccion', { tipo: accion, variant: 'success', icono: 'SaveIcon', texto: 'Guardar', Bclass: 'd-none' })
                //llamar a la funcion que traera el detall 
                // this.detalleVenta(item["vntNumero"])
                // this.$refs["frm-articulo"].show();
            }
            if (accion === "eliminar") {
                this.ControlaEliminar(item)
            }

        },
        UsuarioAlerta(variant, msj) {
            let title, confirmButtonClass, showClass;

            if (variant === "success") {
                title = "Buen Trabajo";
                confirmButtonClass = "btn btn-success";
                showClass = "animate__animated animate__bounceIn";
            } else if (variant === "error") {
                title = "¡Error!";
                confirmButtonClass = "btn btn-danger";
                showClass = "btn btn-danger animate__animated animate__rubberBand";
            } else if (variant === "warning") {
                title = "Precaución";
                confirmButtonClass = "btn btn-warning";
                showClass = "animate__animated animate__wobble";
            } else {
                // Puedes agregar más casos según tus necesidades.
            }

            this.$swal({
                title: title,
                text: msj,
                icon: variant,
                customClass: {
                    confirmButton: confirmButtonClass,
                },
                showClass: {
                    confirmButton: showClass,
                },
                buttonsStyling: true,
            });
        },
        ControlaEliminar(item) {
            this.boxTwo = "";
            this.$bvModal
                .msgBoxConfirm(

                    " La Venta con N°:" + item["vntNumero"] + " Con ID " + item["vntId"] + " Serán Eliminados",
                    {
                        title: "Advertencia",
                        size: "sm",
                        okVariant: "success",
                        okTitle: "Continuar",
                        cancelTitle: "Cancelar",
                        cancelVariant: "danger",
                        hideHeaderClose: true,
                        centered: true,
                    }
                )
                .then((value) => {
                    this.boxTwo = value;
                    if (value === true) {
                        this.eliminar(item);
                    }
                });
        },
        eliminar(item) {
            let me = this;
            const axios = require("axios").default;
            const params = new URLSearchParams();
            params.append('vntId', item["vntId"]);
            params.append('userId', this.$store.state.app.UsuarioId);

            var url = "api/auth/InactiveVenta";
            me.isBusy = true;
            axios
                .post(url, params)
                .then(function (response) {

                    if (response.status == 201) {
                        me.UsuarioAlerta("success", response.data.Mensaje);

                        me.isBusy = false;
                        me.listaImportacion();
                    } else {
                        me.UsuarioAlerta("error", response.data.error);
                    }

                })
                .catch((e) => {
                    me.UsuarioAlerta("error", e.response.data.error);
                    console.log("danger", "No se Realizó la Operación: " + e.message);
                });
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;

        },
        limpiarVariables() {
            let me = this;

            me.shows = false;
            me.loaded = false;
            me.Loading = "";
            me.filter = null;
            me.filterOn = [];
            me.gestion = 0;
            me.mes = 0;
            me.iExiste = 0;
            me.estado = "";
        },

        onContext(ctx) {
            // The date formatted in the locale, or the `label-no - date - selected` string
            this.formatted = ctx.selectedFormatted;
            // The following will be an empty string until a valid date is entered
            this.mesReporte = ctx.selectedYMD;
        },


        //Metodos de logica de Negocios
        BuscarArticulo() {
            let me = this;
            const axios = require("axios").default;
            const params = new URLSearchParams();

            params.append('artCodigo', me.txtCodigoInterno);
            me.items = [];

            var url = "api/auth/TraeArticulo";
            me.loaded = false;
            var lista = [];
            axios
                .post(url, params)
                .then(function (response) {
                    var resp = response.data;
                    lista.push({
                        artId: resp[i].artId,
                        codigo: resp[i].codigo,
                        artNombre: resp[i].artNombre,
                        catNombre: resp[i].catNombre,
                        marNombre: resp[i].marNombre,
                        uniNombre: resp[i].uniNombre,
                        artCosto: resp[i].artCosto,
                        artPrecioVenta: resp[i].artPrecioVenta,
                        artCantidad: resp[i].artCantidad,
                        artCantMin: resp[i].badgeColor
                    });
                    me.items = lista;
                    me.loaded = true;
                })
                .catch((e) => {
                    alert("error al obtener los datos Lista Articulo " + e);
                });
        },


        listaImportacion() {
            let me = this;
            const axios = require("axios").default;
            me.items = [];
            me.isBusy = true;
            var url = "api/auth/listaImportacion";
            me.loaded = false;
            var lista = [];

            const formData = new FormData();
            formData.append("page", me.currentPage);
            formData.append("perPage", me.perPage);

            axios
                .post(url, formData)
                .then(function (response) {
                    var resp = response.data.data;  // Acceder directamente a response.data

                    me.totalRows = response.data.total;
                    me.currentPage = response.data.current_page;
                    //  me.lastPage = response.data.last_page;
                    var badge = null;
                    for (let i = 0; i < resp.length; i++) {  // Acceder a resp.data
                        if (resp[i].impEstadoImportacion === "EN ESPERA") {
                            badge = "warning";
                        }
                        else {
                            badge = "success";
                        }
                        lista.push({
                            impId: resp[i].impId,
                            impNumero: resp[i].impNumero,
                            provNombre: resp[i].provNombre,
                            almNombreAlmacen: resp[i].almNombreAlmacen,
                            impDescripcion: resp[i].impDescripcion,
                            impFechaElaboracion: resp[i].impFechaElaboracion,
                            impEstadoImportacion: resp[i].impEstadoImportacion,
                            BBadge: badge
                        });
                    }

                    me.items = lista;
                    me.isBusy = false;
                    me.loaded = true;
                })
                .catch((e) => {
                    alert("Error al obtener los datos de importación: " + e);
                });
        },

        obtenerVentasPorFecha() {
            let me = this;
            const axios = require("axios").default;
            me.items = [];
            me.isBusy = true;
            const formData = new FormData();

            formData.append("dia", me.dateDefault);
            var url = "api/auth/Venta";
            me.loaded = false;
            var lista = [];
            axios
                .post(url, formData)
                .then(function (response) {
                    var resp = response.data;
                    for (let i = 0; i < resp.length; i++) {
                        lista.push({
                            vntId: resp[i].vntId,
                            vntNumero: resp[i].vntNumero,
                            cliNombre: resp[i].nombreCliente,
                            vntFechaCreacion: resp[i].vntFechaCreacion,
                            totalVenta: resp[i].totalVenta,
                            FormaPago: resp[i].FormaPago
                        });
                    }
                    me.items = lista;
                    me.isBusy = false;
                    me.loaded = true;
                })
                .catch((e) => {
                    alert("Error al obtener los datos de ventas realizadas: " + e);
                    this.UsuarioAlerta("error", e.message.data.error);

                });
        },
        VerificarAperturaCaja() {
            let me = this;


            const axios = require("axios").default;
            const params = new URLSearchParams();
            params.append('cajId', 1);
            me.items = [];

            var url = "api/auth/usuarioTieneCajaAbierta";
            me.loaded = false;
            var lista = [];
            axios
                .post(url, params)
                .then(function (response) {
                    var resp = response.data;

                    if (response.status === 201) {
                        me.UsuarioAlerta("warning", response.data.mensaje)
                    }
                    // me.items = lista;
                    me.loaded = true;
                })
                .catch((e) => {
                    me.UsuarioAlerta("error", e.response.data.error);
                });
        },

        //este metodo ya no se usa , El cierre se hace del el listado de cajas aperturadas
        CierreCaja() {
            let me = this;


            const axios = require("axios").default;
            const params = new URLSearchParams();
            params.append('cajId', 1);
            params.append('userId', this.$store.state.app.UsuarioId);
            me.items = [];

            var url = "api/auth/cerrarCaja";
            me.loaded = false;
            var lista = [];
            axios
                .post(url, params)
                .then(function (response) {
                    var resp = response.data;

                    if (response.status === 201) {
                        me.UsuarioAlerta("success", response.data.mensaje)
                    }
                    // me.items = lista;
                    me.loaded = true;
                })
                .catch((e) => {
                    me.UsuarioAlerta("error", e.response.data.error);

                });
        },

    },
};
function MesActual(fecha, formato) {
    // 2022-09-02 21:51:48.000000"
    const map = {
        dd: fecha.getDate(),
        mm: fecha.getMonth() + 1,
        yy: fecha.getFullYear().toString(),
        yyyy: fecha.getFullYear(),
        HH: fecha.getHours(),
        MM: fecha.getMinutes(),
        SS: fecha.getSeconds(),
    };
    // HH:MM:SS
    return formato.replace(/yy|mm|dd|HH|MM|SS/gi, (matched) => map[matched]);
}
</script>
<style lang="scss" >
@import '~@resources/scss/vue/libs/vue-select.scss';
@import "~@resources/scss/base/pages/app-ecommerce-details.scss";
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';

.custom-badge {
    display: inline-block;
}
</style>
        