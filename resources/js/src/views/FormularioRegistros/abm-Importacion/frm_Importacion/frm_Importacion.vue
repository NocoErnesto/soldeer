<template>
    <section>

        <b-modal ref="frmCliente" id="frmCliente" ok-title="Cerrar" ok-variant="danger" ok-only size="md" centered
            title="Registro de Cliente" no-close-on-backdrop @ok="cbxCliente" @hidden="cerrarVentana">
            <!-- Diseño del Formulario -->


            <!-- <frm-cliente></frm-cliente> -->


        </b-modal>

        <b-col>
            <b-row>
                <b-col sm="3" md="3" xl="3" class="mb-1">
                    <b-row>
                        <b-col>
                            <b-card b-card border-variant="info">
                                <b-row>
                                    <b-col>
                                        <b-row>
                                            <b-col>

                                                <b-form-group>
                                                    <label for="tipoPago">Fecha de Elaboración:</label>
                                                    <flat-pickr v-model="impFechaElaboracion" class="form-control" />
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-col>
                                                <b-form-group>
                                                    <label class="d-inline d-lg-flex">Proveedor</label>
                                                    <v-select v-model="selectedProveedor" :options="booksProveedor"
                                                        label="title" placeholder="Seleccionar Proveedor">
                                                        <template #option="{ title }">
                                                            <feather-icon icon="ListIcon" size="16"
                                                                class="align-middle mr-50" />
                                                            <span> {{ title }}</span>
                                                        </template>
                                                    </v-select>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-col>
                                                <b-form-group>
                                                    <label class="d-inline d-lg-flex">Almacen Destino</label>
                                                    <v-select v-model="selectedAlmacen" :options="booksAlmacen"
                                                        label="title" placeholder="Seleccionar Almacen Destino">
                                                        <template #option="{ title }">
                                                            <feather-icon icon="ListIcon" size="16"
                                                                class="align-middle mr-50" />
                                                            <span> {{ title }}</span>
                                                        </template>
                                                    </v-select>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-col>
                                                <b-form-group>
                                                    <label class="d-inline d-lg-flex">Tipo Cambio</label>
                                                    <b-form-input v-model="impTC" :state="impTC.length ? true : false"
                                                        required />
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-col>
                                                <label style="font-size: 1em" class="mb-25">Descripcion</label>
                                                <b-form-textarea :state="impDescripcion.length ? true : false"
                                                    v-model="impDescripcion" rows="4" class="form-textarea" />
                                                <b-badge :variant="impDescripcion.length ? 'success' : 'danger'">
                                                    {{ impDescripcion.length + "/" + 200 }}
                                                </b-badge>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-col>
                                                <b-form-group>
                                                    <label class="d-inline d-lg-flex">Esado Importación</label>
                                                    <v-select v-model="selectedEstadoImp" :options="estadoImportacion"
                                                        label="title" placeholder="Seleccionar  Estado">
                                                        <template #option="{ title, icon }">
                                                            <feather-icon :icon="icon" size="16"
                                                                class="align-middle mr-50" />
                                                            <span> {{ title }}</span>
                                                        </template>
                                                    </v-select>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-col>
                                                <b-form-group>
                                                    <label class="d-inline d-lg-flex">Total a Pagar</label>
                                                    <b-form-input v-model="totalPagarCalculado" required disabled
                                                        class="montos" />
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col sm="9" md="9" xl="9" lg="9" class="mb-1">
                    <b-row>
                        <b-col sm="12" md="12" xl="12" class="mb-1">
                            <b-row>
                                <b-col>
                                    <b-card border-variant="info">
                                        <b-row>
                                            <b-col sm="9" md="9" xl="9">
                                                <b-form-group>
                                                    <label class="d-inline d-lg-flex">Buscar Producto</label>
                                                    <v-select ref="selectedProductos" v-model="selectedProductos"
                                                        :options="booksProductos" label="title" placeholder="Seleccionar"
                                                        class="select-size-lg" :max-options="3" @input="cargarProducto()">
                                                        <template #option="{ title, icon, precioP, cantidad, marca }">

                                                            <div class="d-flex align-items-center">
                                                                <div class="product-image-container">
                                                                    <img v-if="icon" :src="'data:image/jpeg;base64,' + icon"
                                                                        alt="Sin Foto" />
                                                                </div>
                                                                <div class="product-details">
                                                                    <strong>{{ title }}</strong>
                                                                    <div class="text-secondary ">Precio: {{ precioP }}</div>
                                                                    <div class="text-secondary ">Stock: {{ cantidad }}</div>
                                                                    <div class="text-secondary">Marca:{{ marca }}</div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </v-select>
                                                </b-form-group>
                                            </b-col>
                                            <b-col sm="3" md="3" xl="3">
                                                <b-form-group>
                                                    <div class="d-inline d-lg-flex"> <br></div>
                                                    <b-button v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                                                        v-b-toggle.sidebar-right variant="outline-success">
                                                        Gestionar Gastos
                                                    </b-button>
                                                    <b-sidebar id="sidebar-right" bg-variant="white" right backdrop shadow
                                                        width="600px">
                                                        <frm-caja></frm-caja>
                                                    </b-sidebar>
                                                </b-form-group>


                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <b-col>
                                                <!-- Tabla --> <!-- Listado -->
                                                <b-table id="tabla-lista-retrasos" :items="itemsAgregado"
                                                    :fields="fieldsAgregado" :filter="filter" @filtered="onFiltered" hover
                                                    :busy="isBusy" :bordered="true" outlined stacked="sm" small
                                                    :style="{ fontSize: fontSize }">

                                                    <template #cell(cantidad)="row">
                                                        <b-form-input v-model="row.value" type="number" min="1"
                                                            @input="actualizarCantidad(row.item, row.value)"
                                                            ref="cantidadInput" :state="row.value > 0 ? true : false"
                                                            v-b-tooltip.hover.top.right="row.value > 0 ? '' : 'Cantidad debe ser mayor que cero'"
                                                            :show="row.value === 0" :trigger="'hover focus'"
                                                            class="v-b-tooltip-dark text-center" />
                                                    </template>

                                                    <template #cell(precioP)="row">
                                                        <b-form-input v-model="row.value" type="number" min="1"
                                                            @input="precioProveedor(row.item, row.value)" ref="precioPInput"
                                                            :state="row.value > 0 ? true : false"
                                                            v-b-tooltip.hover.top.right="row.value > row.item.precioP ? 'Precio No Valido' : 'Cantidad debe ser mayor que cero'"
                                                            :show="row.value === 0" :trigger="'hover focus'"
                                                            class="v-b-tooltip-dark text-center" />
                                                    </template>

                                                    <!-- @input="precioProveedor(row.item, row.value)" -->
                                                    <template #cell(precioA)="row">
                                                        <b-form-input v-model="row.value" type="number" min="1"
                                                            ref="precioAInput" :state="row.value > 0 ? true : false"
                                                            v-b-tooltip.hover.top.right="row.value > row.item.precioA ? 'Precio No Valido' : 'Cantidad debe ser mayor que cero'"
                                                            :show="row.value === 0" :trigger="'hover focus'"
                                                            class="v-b-tooltip-dark text-center" />
                                                    </template>
                                                    <template #cell(subtotal)="row">
                                                        {{ row.item.precioP * row.item.cantidad }}
                                                    </template>
                                                    <template #cell(Acción)="row">

                                                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                                                            variant="flat-danger" class="btn-icon rounded-circle"
                                                            :class="{ 'd-none': $store.state.app.isElimina }"
                                                            @click="clickAccion(row.item, row.index, ('eliminar'))">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </template>
                                                    <template #table-busy>
                                                        <div class="text-center text-danger my-2">
                                                            <b-spinner class="align-middle"></b-spinner>
                                                            <strong>Cargando...</strong>
                                                        </div>
                                                    </template>
                                                </b-table>

                                            </b-col>

                                        </b-row>
                                    </b-card>
                                </b-col>
                            </b-row>
                            <b-row>
                                <b-col>
                                    <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" :variant="$store.state.app.variant"
                                        :class="$store.state.app.classButton"
                                        @click="validaOperacion($store.state.app.TipoAccion)">
                                        <feather-icon :icon="$store.state.app.botonIcono" class="mr-50" />
                                        <span class="align-middle">{{ $store.state.app.botonTexto }} </span>
                                    </b-button>
                                </b-col>

                            </b-row>
                        </b-col>

                    </b-row>
                </b-col>
            </b-row>
        </b-col>
    </section>
</template>
<script>
import {
    VBTooltip,
    BFormSpinbutton,
    BImg,
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
    BIconNutFill,
    BInputGroupPrepend,
    BSidebar, VBToggle
} from "bootstrap-vue";
import Ripple from "vue-ripple-directive";
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import Cleave from 'vue-cleave-component'
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import FrmCaja from '../../abm-Caja/FormularioCaja/frmCaja.vue';
import flatPickr from 'vue-flatpickr-component'
//import FrmCliente from '../abm-Cliente/frmCliente.vue';
export default {
    components: {
        flatPickr,
        BSidebar, VBToggle,
        BInputGroupPrepend,
        Cleave,
        VBTooltip,
        ToastificationContent,
        BImg,
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
        BFormSpinbutton,
        FrmCaja,// FrmCliente
        flatPickr
    },
    data() {
        return {
            impTC: "",
            impDescripcion: "",
            impFechaElaboracion: null,
            tiposPago: [],
            SelectedtipoPago: null, // Tipo de pago seleccionado
            gntCliente: [],
            selectedCliente: null,
            tipoVenta: [{ id: "0", title: "CONTADO" }, { id: "1", title: "CREDITO" }],
            selectedTipoVenta: null,
            estadoImportacion: [{ id: 1, title: "EN ESPERA", icon: 'ListIcon', }, { id: 2, title: "RECIVIDO", icon: 'ListIcon', }],
            selectedEstadoImp: null,

            totalPagar: 0, // Total a pagar
            montoRecibido: 0,// Monto recibido
            cjtReferencia: 0,
            cambio: 0, // Cambio a entregar
            vntNumero: "",
            txtFechaVenta: null,
            isBusy: false,
            filter: "",
            stickyHeader: true,
            headVariant: "dark",
            selectedProductos: null,
            booksProductos: [
            ],
            itemsAgregado: [],
            fieldsAgregado: [
                { key: "id", label: "codigo", sortable: true, tdClass: "text-center text-bold" },
                { key: "title", label: "PRODUCTO", sortable: true, tdClass: "text-left" },
                { key: "precioP", label: "COSTO PROVEEDOR", sortable: true, tdClass: "text-center text-bold" },
                { key: "precioA", label: "COSTO EN ALMACEN", sortable: true, tdClass: "text-center text-bold" },
                { key: "cantidad", label: "CANTIDAD", sortable: false, tdClass: "text-center text-bold" },
                { key: "subtotal", label: "SUBTOTAL", sortable: false, tdClass: "text-center text-bold" },
                { key: "Acción", sortable: false, tdClass: "text-center" },
            ],
            selectedProveedor: null,
            booksProveedor: [],
            selectedAlmacen: null,
            booksAlmacen: []

        }
    },
    watch: {
        montoRecibido: function (nuevoMonto) {
            // Calcular el cambio
            this.cambio = nuevoMonto - this.totalPagar

        }



    },
    directives: {
        "b-tooltip": VBTooltip,
        Ripple,
        'b-toggle': VBToggle,
    },

    mounted() {
        this.cbxArticulo()
        this.cbxFormaPago()
        this.cbxCliente()
        this.cbxProveedor()
        this.cbxAlmacen()



        const movil = window.innerWidth;
        if (movil <= 576) {
            // Dispositivo móvil pequeño
            this.fontSize = 'xx-small'; // Tamaño de fuente pequeño
        }


        if (this.$store.state.app.TipoAccion === "gasto") {

            this.TraeImportacion()
        }

    },
    computed: {

        totalPagarCalculado() {
            return this.itemsAgregado.reduce((total, item) => {
                if (item.precioP !== 0) {
                    this.totalPagar = total + item.precioP * item.cantidad;
                } else {
                    this.totalPagar = total + item.precioP * item.cantidad;
                }
                this.montoRecibido = 0;
                this.cambio = 0;
                return this.totalPagar;
            }, 0);
        },
    },
    methods:
    {

        async cbxAlmacen() {
            try {
                let me = this;
                const lista = [];
                me.booksAlmacen = []
                const response = await this.$http.get("listarAlmacen")
                const resp = response.data;
                for (let i = 0; i < resp.length; i++) {
                    lista.push({
                        id: resp[i].almId,
                        title: resp[i].almNombreAlmacen,
                    });
                }
                me.booksAlmacen = lista;
            } catch (error) {
                console.log(error.message);
                this.UsuarioAlerta("error", error.response);
                this.showOverlay = false;

            }

        },
        cbxProveedor() {
            let me = this;
            const axios = require("axios").default;
            me.isBusy = true;
            var url = "api/auth/listarProveedores";
            me.loaded = false;
            var lista = [];
            axios.get(url).then(function (response) {
                var resp = response.data;
                for (let i = 0; i < resp.length; i++) {
                    lista.push({
                        id: resp[i].provID,
                        title: resp[i].provNombre + " - " + resp[i].provTelefono,
                    });
                }
                me.booksProveedor = lista;
                me.isBusy = false;
                me.loaded = true;
            }).catch((e) => {
                AlertaMensaje("error", "Obtener Datos de Proveedores, Detalles: " + e.response.data.error);
            });
        },

        generatePDF(Articulo) {
            try {
                // Crear un nuevo documento PDF
                const doc = new jsPDF();
                let me = this;

                // Agregar el logo de la empresa (reemplaza 'ruta_al_logo' con la ruta de tu imagen)
                // const image = new Image();
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
                const formattedDate = this.impFechaElaboracion// currentDate.toLocaleDateString('es-ES', options); // Formatea la fecha como "10/11/2023"

                // Configuración de la nota de venta
                const notaImportacion = {
                    numero: me.cjtReferencia,
                    fecha: formattedDate,
                    proveedor: me.selectedProveedor.title,
                    direccion: '123 Calle Principal',
                    ciudad: 'Ciudad Ejemplo',
                };

                debugger
                // Datos de los productos
                const columns = ['Articulo', 'Cantidad', 'Precio Proveedor', 'SubTotal'];
                const rows = Articulo.map((producto) => {
                    const precioOriginal = parseFloat(producto.precioP);
                    // const precioUnitario = producto.descuento ? parseFloat(producto.descuento) : precioOriginal;
                    const subtotal = parseFloat(producto.precioP) * parseFloat(producto.cantidad);

                    return [
                        producto.title || '',
                        producto.cantidad || 0,
                        precioOriginal.toFixed(2), // Mostrar el precio original en la columna 'Precio'
                        // producto.descuento || 0,
                        subtotal.toFixed(2),
                    ];
                });

                // Agregar el encabezado de la nota de venta
                doc.setFontSize(22);
                doc.setFont('helvetica', 'neue');
                doc.text('Nota de Importación', 135, 10);
                doc.setFontSize(14);
                doc.setFont('helvetica', 'neue');

                doc.text('N°:', 135, 20);
                // doc.setTextColor(110, 107, 123);
                doc.text(`${notaImportacion.numero}`, 145, 20);
                doc.setTextColor(0);
                doc.setFont('helvetica', 'neue');
                doc.text(`Fecha: ${notaImportacion.fecha}`, 135, 28);
                doc.setFont('helvetica', 'neue');
                // doc.setTextColor(0);
                doc.text("Cliente:", 15, 52);
                // doc.setTextColor(100);
                doc.setFont('helvetica', 'neue');
                doc.text(`${notaImportacion.proveedor}`, 35, 52);
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
                // const total = Articulo.reduce((acc, producto) => acc + parseFloat(producto.precioP) * parseInt(producto.cantidad), 0);
                const total = Articulo.reduce((acc, producto) => {
                    const precioUnitario = parseFloat(producto.precioP);
                    const subtotal = precioUnitario * parseFloat(producto.cantidad);
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
                this.UsuarioAlerta("error", "Error al generar el PDF: " + error.message);
            }
        }
        ,

        cerrarVentana() {
            this.cbxCliente()
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

        //eventos 
        cbxFormaPago() {
            let me = this;
            const axios = require("axios").default;
            const params = new URLSearchParams();
            // params.append('email', me.email);
            // me.tiposPago = [];
            me.isBusy = true;
            var url = "api/auth/ListaFormaPago";
            me.loaded = false;
            var lista = [];

            axios
                .get(url)
                .then(function (response) {
                    var resp = response.data;
                    for (let i = 0; i < resp.length; i++) {

                        lista.push({
                            id: resp[i].fpId,
                            title: resp[i].fpnombre,
                        });
                    }
                    me.tiposPago = lista;
                    me.isBusy = false;
                    me.loaded = true;
                })
                .catch((e) => {
                    UsuarioAlerta("error", e.response.data.error);
                });

        },


        cbxArticulo() {
            let me = this;
            const axios = require("axios").default;
            const params = new URLSearchParams();
            // params.append('email', me.email);
            me.items = [];
            me.isBusy = true;
            var url = "api/auth/listArticulo";
            me.loaded = false;
            var lista = [];
            axios
                .get(url)
                .then(function (response) {
                    var resp = response.data;
                    for (let i = 0; i < resp.length; i++) {
                        lista.push({
                            id: resp[i].artId,
                            title: resp[i].artNombre,
                            icon: resp[i].artFoto,
                            precioP: resp[i].artCostoProveedor,
                            cantidad: resp[i].artCantidad,
                            marca: resp[i].marNombre
                        });
                    }
                    me.booksProductos = lista;
                    me.isBusy = false;
                    me.loaded = true;
                })
                .catch((e) => {
                    alert("error al obtener los datos Lista Articulo " + e);
                });
        },


        cbxCliente() {

            let me = this;
            const axios = require("axios").default;
            const params = new URLSearchParams();
            // params.append('email', me.email);
            me.items = [];
            me.isBusy = true;
            var url = "api/auth/ListaCliente";
            me.loaded = false;
            var lista = [];
            axios
                .get(url)
                .then(function (response) {
                    var resp = response.data;
                    for (let i = 0; i < resp.length; i++) {
                        lista.push({
                            id: resp[i].cliId,
                            title: resp[i].cliNombre + " " + resp[i].cliApp + " " + resp[i].cliApm,
                        });
                    }
                    me.gntCliente = lista;
                    me.isBusy = false;
                    me.loaded = true;
                })
                .catch((e) => {
                    alert("error al obtener los datos Lista Articulo " + e);
                });

        },
        cargarProducto() {

            if (this.selectedProductos) {
                // Verificar si el producto ya está en la lista itemsAgregado
                const productoExistente = this.itemsAgregado.find(item => item.id === this.selectedProductos.id);

                if (!productoExistente) {
                    // Si el producto no existe en la lista, lo agregamos
                    this.itemsAgregado.push({ ...this.selectedProductos, precioP: 0, precioA: 0, cantidad: 1, subtotal: this.selectedProductos.precioP });
                } else {
                    // Si el producto ya existe, puedes mostrar un mensaje de alerta o realizar otra acción
                    this.UsuarioAlerta("error", 'El producto ya está en la lista');

                }

                // Resetea el valor de selectedProductos para deseleccionar la opción
                this.selectedProductos = null;
                this.$nextTick(() => {
                    this.$refs.cantidadInput.focus();

                });

            }
        },

        //Metodos Con logica de Negocios

        vaciarControles() {

            const me = this
            me.selectedCliente = null
            me.selectedTipoVenta = null
            me.SelectedtipoPago = null
            me.itemsAgregado = []
            me.montoRecibido = 0
            me.cambio = 0
            me.txtFechaVenta = null
            me.cbxArticulo();
        },

        GurdarMovimientoCaja() {

            const me = this;
            const axios = require("axios").default;
            const formData = new FormData();

            formData.append("cajId", 2);
            formData.append("userId", this.$store.state.app.UsuarioId); // ID del usuario actual
            formData.append("cjtReferencia", me.cjtReferencia);
            formData.append("cjtModulo", "Importación");
            formData.append("cjtMonto", "-" + me.totalPagar)
            formData.append("cjtDescripcion", "Gastos Por Importación");
            formData.append("ttxnId", 6);
            axios
                .post("api/auth/guardarMovimientoCaja", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(function (response) {
                    if (response.status === 201) {
                        console.log(response.data.mensaje)

                    }
                })
                .catch((e) => {
                    me.showOverlay = false;
                    me.UsuarioAlerta("error", e.response.data.error);
                });


        },


        Guardar() {
            const me = this;
            me.showOverlay = true;
            const hoy = new Date();
            const axios = require("axios").default;
            const formData = new FormData();

            // if (me.selectedAlmacen === null || me.selectedProveedor === null || me.selectedEstadoImp === null || me.txtFechaVenta === null) {
            //     return me.UsuarioAlerta("error", "Faltan Datos Para Ingresar")
            // }

            formData.append("impTC", this.impTC); //ID del tipo de transacción 
            formData.append("provId", this.selectedProveedor.id); //  ID del proveedor
            formData.append("almId", this.selectedAlmacen.id)
            formData.append("impDescripcion", this.impDescripcion)
            formData.append("ttxId", 6); //ID del tipo de transacción 
            formData.append("impFechaElaboracion", this.impFechaElaboracion);
            formData.append("impEstadoImportacion", this.selectedEstadoImp.title); // ID del usuario actual
            formData.append("userId", this.$store.state.app.UsuarioId)
            formData.append("impActivo", 1); // Esto puede ser 1 o 0 según corresponda (activo o no)
            // Construye un array de detalles de venta
            const detalleImportacion = this.itemsAgregado.map(item => ({
                artId: item.id, // ID del artículo
                dImpCantidad: item.cantidad, // Cantidad Importada
                dImpPrecioUnitario: item.precioP, // Precio del Proveedor
                dImpCostoUnitario: item.precioA, // costo den almacen
                dImpActivo: 1, // Activo (ajusta según necesites)
                dImpFechaCreacion: this.txtFechaVenta
            }));
            if (detalleImportacion.length <= 0) {
                return me.UsuarioAlerta("error", "Debe Selecinar Ariculos")

            }
            formData.append("detalleImportacion", JSON.stringify(detalleImportacion));
            axios
                .post("api/auth/guardarImportacion", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(function (response) {
                    if (response.status === 201) {
                        me.showOverlay = false;
                        me.UsuarioAlerta("success", response.data.mensaje);
                        me.cjtReferencia = response.data.cjtReferencia;
                        me.GurdarMovimientoCaja()

                        me.generatePDF(me.itemsAgregado)
                        me.isBusy = false;
                        me.vaciarControles()
                    }
                })
                .catch((e) => {
                    if (!e.response || !e.response.data || !e.response.data.error) {
                        // No hay error específico
                        me.showOverlay = false;
                        // me.UsuarioAlerta("error", "Error al Generar PDF" + e.message);
                    } else {
                        // Hay un mensaje de error específico
                        me.showOverlay = false;

                        me.UsuarioAlerta("error", e.response.data.error);
                    }
                    // me.showOverlay = false;
                    // me.UsuarioAlerta("error", e.response ? e.response.data.error : "Error desconocido");
                });
        },
        actualizarCantidad(item, nuevaCantidad) {
            item.cantidad = nuevaCantidad;

        },

        // no se usa ya que no se sabe si tendremos precioPs en la importaciones
        precioProveedor(item, precioProveedor) {
            item.precioP = precioProveedor

        },

        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
        },
        eliminarProducto(index) {
            if (index >= 0 && index < this.itemsAgregado.length) {
                this.itemsAgregado.splice(index, 1);
                this.montoRecibido = 0
                this.cambio = 0

            }
        },
        ControlaEliminar(item, index) {

            this.boxTwo = "";
            this.$bvModal
                .msgBoxConfirm(
                    "El Registro  " + " : " + item["title"] + " Serán Eliminados",
                    {
                        title: "Advertencia",
                        size: "sm",
                        okVariant: "outline-success",
                        okTitle: "Continuar",
                        cancelTitle: "Cancelar",
                        cancelVariant: "outline-danger",
                        hideHeaderClose: true,
                        centered: true,


                    }
                )
                .then((value) => {
                    this.boxTwo = value;
                    if (value === true) {
                        this.eliminarProducto(index);
                    }
                });
        },

        //Realiza la Operacion Correspondiente
        validaOperacion(accion) {
            if (accion === "guardar") { this.Guardar() }
            if (accion === "editar") { this.mofificar() }
            if (accion === "ver") { alert("ajecutara el ver") }

        },
        //Este evento elimina Articulo del Carrito
        clickAccion(item, index, accion) {
            if (accion === "eliminar") {
                this.ControlaEliminar(item, index)
            }
        },
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import "~@resources/scss/base/pages/app-ecommerce-details.scss";
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';

.product-image-container {
    width: 60px;
    height: 60px;
    margin-right: 10px;
    overflow: hidden;
    border-radius: 4px;
}

.product-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-details {
    flex-grow: 1;
    margin-left: 10px;

    /* Agrega un margen a la izquierda */
}

/* Estilos para dispositivos móviles */
@media screen and (max-width: 767px) {
    .form-textarea {
        font-size: 16px;
        /* Tamaño de letra para dispositivos móviles */
    }
}

/* Estilos para dispositivos no móviles */
@media screen and (min-width: 768px) {
    .form-textarea {
        font-size: 15px;
        /* Tamaño de letra para dispositivos no móviles */
    }
}

.form-textarea {
    text-transform: uppercase;
}


//para los totales ,recibido, cambios
.montos {
    font-size: 24px;
    /* Ajusta el tamaño de fuente según tus preferencias */
    font-weight: bold;
    /* Hace que el texto sea negrita */
    color: #007bff;
    /* Cambia el color del texto, si lo deseas */
    /* Agrega otros estilos según tus preferencias */
}
</style>