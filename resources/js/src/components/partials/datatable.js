/**
 * Remove null , undefined and empty elements from Array.
 * @param items Array
 * @returns {*}
 */

export function compact(items) {
    return items.filter(item => (item !== null && item !== undefined && "" !== (isString(item) ? item.trim() : item)));
}

/**
 * @example obj2Table(object, ['column1','column2'])
 * @param items Object of the Data
 * @param skip Array Columns to be skipped / rejected
 * @param as
 * @returns {Array}
 */
export function obj2Table(items, skip = [], as = {key: 'key', value: 'value'}) {
    let output = [];
    for (let x in items) {
        if (!skip.includes(x)) {
            let ret = {};
            ret[as['key']] = x;
            ret[as['value']] = items[x];
            output.push(ret);
        }
    }
    return output;
}

/**
 * Object to Array of Objects Convertion
 * @param items
 * @param skip Array , Objects fields that needs to be skipped from output object
 * @param callback (key,value)=>{return {key:key, value: value}}
 * @returns {[]}
 */
export function objToArrObj(items, skip = [], callback) {
    let output = [];
    for (let x in items) {
        if (!skip.includes(x)) {
            output.push(callback(x, items[x]));
        }
    }
    return output;
}

export function changeVisibility(field, index) {
    if (typeof (this.fields[index].thClass) !== "string") {
        this.fields[index].thClass = '';
    }
    if (typeof (this.fields[index].tdClass) !== "string") {
        this.fields[index].tdClass = '';
    }
    if (field.visible) {
        this.fields[index].thClass += ' d-none';
        this.fields[index].tdClass += ' d-none';

    } else {
        this.fields[index].thClass = this.fields[index].thClass.replace('d-none', '').trim();
        this.fields[index].tdClass = this.fields[index].tdClass.replace('d-none', '').trim();
    }
}

export function msgBox(data, duration, append) {
    this.$bvToast.toast(data.msg, {
        title: data.title,
        variant: data.type,
        autoHideDelay: duration || 3000,
        appendToast: append || false
    });
}

export function range(step = 10, quantity = 50) {
    return [...Array(quantity).keys()].map(i => i * step + step);
}

export function isBoolean(value) {
    return value === true || value === false;
}

export function isString(value) {
    return typeof value === "string";
}

/**
 * Initializng Datatable Columns Visibilty Toggle Feature
 * @param scope
 */
export function iniDatatableVisibility(scope) {
    scope.fields.forEach(item => {
        if (!isBoolean(item.visible)) {
            scope.$set(item, 'visible', true);
        }

        if (isBoolean(item.visible) && !item.visible) {
            scope.$set(item, 'thClass', 'd-none');
            scope.$set(item, 'tdClass', 'd-none');
        }
    });
}

/**
 * What happens when dropdown hides
 * @param scope
 * @param callprev
 * @param prev_params
 * @param callback
 * @param params
 */
export function initDatatableModalEvents(scope, callprev, prev_params = [], callback, params = []) {
    if (typeof callprev === "function") {
        callprev(...prev_params);
    } else {
        scope.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
            if (modalId === 'addModal') {
                scope.form = {};
            } else if (modalId === 'viewModal') {
                scope.currentItem = {};
            }
            if (typeof callback === "function") {
                callback(...params);
            }
        })
    }
}

export function setColumns(scope) {
    return compact(scope.fields.map(item => {
        if (isBoolean(item.visible) && item.visible) {
            if (!isBoolean(item.searchable) || item.searchable) {
                return item.key;
            }
        }
    }));
}

export function isTrue(value) {
    return isBoolean(value) && value;
}

export function isArray(value) {
    return Array.isArray(value);
}

/**
 * Forked From : /node_modules/bootstrap-vue/esm/utils/startcase.js
 * Converts a string, including strings in camelCase or snake_case, into Start Case (a variant
 * of Title Case where all words start with a capital letter), it keeps original single quote
 * and hyphen in the word.
 *
 * Copyright (c) 2017 Compass (MIT)
 * https://github.com/UrbanCompass/to-start-case
 * @author Zhuoyuan Zhang <https://github.com/drawyan>
 * @author Wei Wang <https://github.com/onlywei>
 *
 *
 *   'management_companies' to 'Management Companies'
 *   'managementCompanies' to 'Management Companies'
 *   `hell's kitchen` to `Hell's Kitchen`
 *   `co-op` to `Co-op`
 *
 * @param {String} str
 * @returns {String}
 */
// Precompile regular expressions for performance
let RX_UNDERSCORE = /_/g;
let RX_LOWER_UPPER = /([a-z])([A-Z])/g;
let RX_START_SPACE_WORD = /(\s|^)(\w)/g;

export function startCase(str) {
    return str.replace(RX_UNDERSCORE, ' ')
        .replace(RX_LOWER_UPPER, (str, $1, $2) => ($1 + ' ' + $2))
        .replace(RX_START_SPACE_WORD, (str, $1, $2) => ($1 + $2.toUpperCase()));
}


export default {
    data() {
        return {
            search: '',
            currentItem: {},
            datatable: {
                per_page: 10,
                current_page: 1,
                total: 0,
                from: 0,
                to: 0,
            },
            fields: [],
        };
    },
    mounted() {
        iniDatatableVisibility(this);
        initDatatableModalEvents(this);
    },
    methods: {
        getItems(ctx) {
            // console.log(ctx)
            return axios.post(this.api_url + "?page=" + (ctx.currentPage ? ctx.currentPage : 1), {
                per_page: this.datatable.per_page,
                orderBy: ctx.sortBy || 'id',
                order: isTrue(ctx.sortDesc) ? 'desc' : 'asc',
                filter: ctx.filter,
                columns: setColumns(this)
            }).then(res => {
                // console.log(res);
                this.datatable.total = res.data.total;
                this.datatable.from = res.data.from;
                this.datatable.to = res.data.to;
                this.datatable.current_page = res.data.current_page;
                return res.data.data;
            }).catch(err => {
                console.log(err.response);
                return [];
            });
        },
        onSubmit(callback = null, callback_params = []) {
            axios.post(this.submit_url, this.form, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(res => {
                // console.log(res)
                this.$bvModal.hide('addModal');
                this.msgBox(res.data);
                this.$refs.datatable.refresh();
                if (callback) {
                    callback(...callback_params);
                }
            }).catch(err => {
                this.msgBox(err.response);
                console.log(err.response)
            });
        },
        trash(id, datatable = 'datatable', url = null) {
            this.$bvModal
                .msgBoxConfirm('Are you sure?',)
                .then(value => {
                    if (value) {
                        axios.post(url || this.trash_url, {
                            id: id,
                        }).then(res => {
                            this.msgBox(res.data);
                            this.$refs[datatable].refresh();
                        }).catch(err => {
                            this.msgBox(err.response);
                            console.log(err.response)
                        });
                    }
                })
                .catch(err => {
                    console.log(err)
                });
        },
        msgBox,
        obj2Table,
        startCase
    }
}
