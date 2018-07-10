<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">Invoices</h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button
          v-if="
            can('invoice_create_shipping') || can('invoice_create_dispatch')
          "
          class="button text-white bg-theme-1 shadow-md mr-2"
          @click="onClickAdd"
        >
          Add New Invoice
        </button>
      </div>
    </div>
    <!-- BEGIN: invoice Data -->
    <div>
      <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12 lg:col-span-3 xxl:col-span-2">
          <div class="intro-y box p-5">
            <h4 class="flex items-center text-lg">
              <SearchIcon class="mr-2"></SearchIcon>
              <b>Invoice Filter</b>
            </h4>
            <h5 class="my-4">User</h5>
            <Multiselect
              v-model="filter.user"
              :multiple="false"
              :close-on-select="true"
              track-by="id"
              label="uid"
              placeholder="Select user"
              :select-label="'Select'"
              :deselect-label="'Deselect'"
              :options="users"
              :searchable="true"
              :allow-empty="true"
              :loading="isLoadingUsers"
              @search-change="searchUsers"
            >
              <span slot="noResult">No users found.</span>
            </Multiselect>
            <h5 class="my-4">Invoice type</h5>
            <select
              v-model="filter.type"
              class="intro-x input input--md border border-gray-300 block w-full"
            >
              <option value="all">All</option>
              <option value="shipping">Shipping Invoice</option>
              <option value="dispatch">Dispatch Invoice</option>
            </select>
            <h5 class="my-4">Invoice status</h5>
            <select
              v-model="filter.status"
              class="intro-x input input--md border border-gray-300 block w-full"
            >
              <option value="all">All</option>
              <option value="created">Created</option>
              <option value="received">Received</option>
              <option value="dispatched">Dispatched</option>
              <option value="collected">Collected</option>
            </select>
            <h5 class="my-4">Paid/Unpaid</h5>
            <select
              v-model="filter.paid"
              class="intro-x input input--md border border-gray-300 block w-full"
            >
              <option value="all">All</option>
              <option value="paid">Paid</option>
              <option value="unpaid">Unpaid</option>
            </select>
            <button
              class="button rounded-full text-white bg-theme-1 shadow-md mt-2 w-full"
              @click="applyFilter"
            >
              Apply filter
            </button>
            <button
              class="button rounded-full text-white bg-theme-6 shadow-md mt-2 w-full"
              @click="initFilter"
            >
              Reset
            </button>
          </div>
        </div>
        <div class="col-span-12 lg:col-span-9 xxl:col-span-10">
          <div class="intro-y box p-5 h-full">
            <h4 class="flex items-center text-lg">
              <FileTextIcon class="mr-2"></FileTextIcon>
              <b
                >Invoices
                <small class="text-theme-1">Total: {{ total }}</small></b
              >
            </h4>
            <div class="grid grid-cols-12 gap-6 mt-5">
              <h6
                v-if="invoices.length <= 0"
                class="col-span-12 text-center block text-xl mt-5 p-5"
              >
                No invoices.
              </h6>
              <div
                v-for="(inv, index) in invoices"
                v-else
                :key="'a-' + index"
                class="col-span-12 lg:col-span-3 h-full"
              >
                <div
                  class="intro-y box p-5 h-full shadow-lg border-2 invoice-card"
                  :class="!inv.paid ? 'border-red-200' : 'border-gray-100'"
                >
                  <div
                    class="flex flex-col sm:flex-row items-center pb-3 border-b border-gray-400 dark:border-dark-5"
                  >
                    <h2
                      class="text-md text-base mr-auto flex items-center cursor-pointer"
                      title="View this invoice"
                      @click="onClickView(inv)"
                    >
                      <span class="text-theme-3 flex uppercase mr-2">
                        Invoice
                      </span>
                      <b>#{{ pad(inv.id, 5) }}</b>
                      <small
                        v-if="inv.paid"
                        class="flex flex items-center text-theme-9"
                      >
                        <CheckSquareIcon
                          class="w-5 h-5 ml-3 mr-2"
                        ></CheckSquareIcon>
                        Paid
                      </small>
                    </h2>
                    <div
                      v-if="
                        can('invoice_create_shipping') ||
                          can('invoice_create_dispatch')
                      "
                      class="dropdown ml-auto sm:ml-0"
                    >
                      <a
                        class="dropdown-toggle text-white-700 dark:text-gray-300"
                      >
                        <span class="w-5 h-5 flex items-center justify-center">
                          <MenuIcon class="w-5 h-5"></MenuIcon>
                        </span>
                      </a>
                      <div class="dropdown-box w-40 shadow-lg">
                        <div
                          class="dropdown-box__content box dark:bg-dark-1 p-2"
                        >
                          <a
                            class="flex items-center text-theme-7 block p-2 transition duration-300 ease-in-out bg-white dark:bg-dark-1 hover:bg-gray-200 dark:hover:bg-dark-2 rounded-md"
                            @click="onClickEdit(inv)"
                          >
                            <EditIcon class="w-6 h-6 mr-2"></EditIcon> Edit
                          </a>
                          <a
                            v-if="isOwner(inv)"
                            class="flex items-center text-theme-6 block p-2 transition duration-300 ease-in-out bg-white dark:bg-dark-1 hover:bg-gray-200 dark:hover:bg-dark-2 rounded-md"
                            @click="onClickDelete(inv.id)"
                          >
                            <Trash2Icon class="w-6 h-6 mr-2"></Trash2Icon>
                            Remove
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="w-full flex mt-4">
                    <UserIcon class="w-5 h-5 mr-3 text-theme-1"></UserIcon>
                    <div>
                      <p>
                        Name:
                        <b
                          >{{ inv.user.first_name }} {{ inv.user.last_name }}</b
                        >
                      </p>
                      <p>
                        User ID: <b>#{{ inv.user.uid }}</b>
                      </p>
                    </div>
                  </div>
                  <div class="w-full flex mt-4">
                    <ClipboardIcon
                      class="w-5 h-5 mr-3 text-theme-1"
                    ></ClipboardIcon>
                    <div>
                      <p>
                        Type: <b class="uppercase">{{ inv.type }}</b>
                      </p>
                    </div>
                  </div>
                  <div class="w-full flex mt-4">
                    <DollarSignIcon
                      class="w-5 h-5 mr-3 text-theme-1"
                    ></DollarSignIcon>
                    <div>
                      <p>
                        Cost: <b class="mr-3">{{ inv.cost }} ₦</b> Insurance:
                        <b>{{ inv.insurance }} ₦</b>
                      </p>
                    </div>
                  </div>
                  <div class="w-full flex mt-4">
                    <ClockIcon class="w-5 h-5 mr-3 text-theme-1"></ClockIcon>
                    <div>
                      <p>
                        Status: <b class="uppercase">{{ inv.status }}</b>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="isLoadingInvs" class="flex items justify-center mt-5">
              <LoadingIcon icon="tail-spin" class="w-10 h-10" />
            </div>
            <div class="flex items justify-center">
              <button
                v-if="invoices.length < total"
                class="button rounded-full text-gray-600 shadow-lg mt-5 mx-auto px-4"
                @click="loadMore"
              >
                LOAD MORE
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END: invoice Data -->
    <div id="invoice-modal" class="modal">
      <div class="modal__content">
        <div class="intro-y box">
          <div
            class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5"
          >
            <h2 class="font-medium text-base mr-auto">
              {{ isNew ? "Add new invoice" : "Edit invoice" }}
            </h2>
          </div>
          <div class="p-5 preview">
            <div class="grid grid-cols-12 gap-6">
              <div class="col-span-12 md:col-span-6">
                <label class="block mb-2">Select User *</label>
                <Multiselect
                  v-model="invoice.user"
                  :multiple="false"
                  :close-on-select="true"
                  track-by="id"
                  label="uid"
                  return="id"
                  placeholder="Select user"
                  :select-label="'Select'"
                  :deselect-label="'Deselect'"
                  :options="users"
                  :searchable="true"
                  :allow-empty="false"
                  :loading="isLoadingUsers"
                  :disabled="!canEdit"
                  @search-change="searchUsers"
                >
                  <span slot="noResult">No users found.</span>
                </Multiselect>
                <template v-if="$v.invoice.user.$error">
                  <div
                    v-if="!$v.invoice.user.required"
                    class="text-theme-6 mt-2"
                  >
                    Field is required
                  </div>
                </template>
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="block mb-2">Type *</label>
                <select
                  v-model="invoice.type"
                  class="intro-x input input--md border border-gray-300 block w-full"
                  :disabled="!can('invoice_create_shipping')"
                >
                  <option value="shipping">Shipping Invoice</option>
                  <option value="dispatch">Dispatch Invoice</option>
                </select>
                <template v-if="$v.invoice.type.$error">
                  <div
                    v-if="!$v.invoice.type.required"
                    class="text-theme-6 mt-2"
                  >
                    Field is required
                  </div>
                </template>
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="block mb-2">Status *</label>
                <select
                  v-model="invoice.status"
                  class="intro-x input input--md border border-gray-300 block w-full"
                >
                  <option value="created">Created</option>
                  <option value="received">Received</option>
                  <option value="dispatched">Dispatched</option>
                  <option value="collected">Collected</option>
                </select>
                <template v-if="$v.invoice.status.$error">
                  <div
                    v-if="!$v.invoice.status.required"
                    class="text-theme-6 mt-2"
                  >
                    Field is required
                  </div>
                </template>
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="block mb-2">Weight *</label>
                <div class="relative mt-2">
                  <input
                    v-model="invoice.weight"
                    type="number"
                    class="intro-x input input--md border border-gray-300 pr-12 w-full col-span-4"
                    placeholder="Weight"
                    :disabled="!canEdit"
                  />
                  <div
                    class="absolute top-0 right-0 rounded-r w-10 h-full flex items-center justify-center bg-gray-100 dark:bg-dark-1 dark:border-dark-4 border text-gray-600 z-50 cursor-pointer"
                    @click="toggleWeightUnit"
                  >
                    {{ invoice.weight_unit }}
                  </div>
                </div>
                <template v-if="$v.invoice.weight.$error">
                  <div
                    v-if="!$v.invoice.weight.required"
                    class="text-theme-6 mt-2"
                  >
                    Field is required
                  </div>
                </template>
              </div>
              <div class="col-span-12 md:col-span-4">
                <label class="block mb-2">Width</label>
                <div class="relative mt-2">
                  <input
                    v-model="invoice.width"
                    type="number"
                    class="intro-x input input--md border border-gray-300 pr-12 w-full col-span-4"
                    placeholder="Width"
                    :disabled="!canEdit"
                  />
                  <div
                    class="absolute top-0 right-0 rounded-r w-10 h-full flex items-center justify-center bg-gray-100 dark:bg-dark-1 dark:border-dark-4 border text-gray-600 z-50 cursor-pointer"
                  >
                    m
                  </div>
                </div>
              </div>
              <div class="col-span-12 md:col-span-4">
                <label class="block mb-2">Height</label>
                <div class="relative mt-2">
                  <input
                    v-model="invoice.height"
                    type="number"
                    class="intro-x input input--md border border-gray-300 pr-12 w-full col-span-4"
                    placeholder="Height"
                    :disabled="!canEdit"
                  />
                  <div
                    class="absolute top-0 right-0 rounded-r w-10 h-full flex items-center justify-center bg-gray-100 dark:bg-dark-1 dark:border-dark-4 border text-gray-600 z-50 cursor-pointer"
                  >
                    m
                  </div>
                </div>
              </div>
              <div class="col-span-12 md:col-span-4">
                <label class="block mb-2">Total Value</label>
                <input
                  v-model="invoice.total"
                  type="number"
                  class="intro-x input input--md border border-gray-300 block w-full"
                  placeholder="Total Value"
                  :disabled="!canEdit"
                />
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="block mb-2">Cost *</label>
                <div class="relative mt-2">
                  <input
                    v-model="invoice.cost"
                    type="number"
                    class="intro-x input input--md border border-gray-300 pr-12 w-full col-span-4"
                    placeholder="Cost"
                    :disabled="!canEdit"
                  />
                  <div
                    class="absolute top-0 right-0 rounded-r w-10 h-full flex items-center justify-center bg-gray-100 dark:bg-dark-1 dark:border-dark-4 border text-gray-600 z-50 cursor-pointer"
                  >
                    ₦
                  </div>
                </div>
                <template v-if="$v.invoice.cost.$error">
                  <div
                    v-if="!$v.invoice.cost.required"
                    class="text-theme-6 mt-2"
                  >
                    Field is required
                  </div>
                </template>
              </div>
              <div class="col-span-12 md:col-span-6">
                <label class="block mb-2">Insurance</label>
                <div class="relative mt-2">
                  <input
                    v-model="invoice.insurance"
                    type="number"
                    class="intro-x input input--md border border-gray-300 pr-12 w-full col-span-4"
                    placeholder="Insurance"
                    :disabled="!canEdit"
                  />
                  <div
                    class="absolute top-0 right-0 rounded-r w-10 h-full flex items-center justify-center bg-gray-100 dark:bg-dark-1 dark:border-dark-4 border text-gray-600 z-50 cursor-pointer"
                  >
                    ₦
                  </div>
                </div>
                <template v-if="$v.invoice.insurance.$error">
                  <div
                    v-if="!$v.invoice.insurance.required"
                    class="text-theme-6 mt-2"
                  >
                    Field is required
                  </div>
                </template>
              </div>

              <div class="col-span-12">
                <label
                  class="block mb-2 mt-4 flex align-items-center justify-between"
                >
                  <span class="mr-auto">Notes</span>
                  <button
                    class="button px-2 bg-theme-1 text-white right-0 top-0 h-full z-50 w-50"
                    @click="addNote"
                  >
                    <span class="w-5 h-5 flex items-center justify-center">
                      <PlusIcon class="w-4 h-4" />
                    </span>
                  </button>
                </label>
                <div
                  v-for="(note, index) in invoice.notes"
                  :key="'note-' + index"
                  class="phone relative mb-2"
                >
                  <textarea
                    v-model="invoice.notes[index]"
                    type="text"
                    class="intro-x login__input input input--md border border-gray-300 block w-full"
                  />
                  <button
                    class="button px-2 bg-theme-6 text-white absolute right-0 top-0 z-50"
                    @click="removeNote(index)"
                  >
                    <span class="w-5 h-5 flex items-center justify-center">
                      <XIcon class="w-4 h-4" />
                    </span>
                  </button>
                </div>
              </div>
            </div>

            <div class="w-full flex justify-end">
              <button
                type="button"
                class="button bg-theme-1 text-white mt-5 w-24"
                @click="onClickSave"
              >
                Save
              </button>
              <button
                v-if="canEdit"
                type="button"
                class="button bg-gray-200 text-white mt-5 ml-5 w-24 text-gray-600"
                @click="initInvoice"
              >
                Reset
              </button>
              <button
                type="button"
                class="button bg-gray-200 bg-theme-6 text-white mt-5 ml-5 w-24"
                @click="closeModal('invoice-modal')"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="delete-modal" class="modal">
      <div class="modal__content">
        <div class="p-5 text-center">
          <xCircleIcon class="w-16 h-16 text-theme-6 mx-auto mt-3" />
          <div class="text-3xl mt-5">Are you sure?</div>
          <div class="text-gray-600 mt-2">
            Do you really want to delete these records? This process cannot be
            undone.
          </div>
        </div>
        <div class="px-5 pb-8 text-center">
          <button
            type="button"
            data-dismiss="modal"
            class="button w-24 border text-gray-700 dark:border-dark-5 dark:text-gray-300 mr-1"
            @click="closeModal('delete-modal')"
          >
            Cancel
          </button>
          <button
            type="button"
            class="button w-24 bg-theme-6 text-white"
            @click="onConfirmDelete"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <div id="view-modal" class="modal">
      <div
        v-if="invToView"
        class="modal__content modal__content--xl p-10 text-center"
      >
        <div class="intro-y box overflow-hidden">
          <div
            class="border-b border-gray-200 dark:border-dark-5 text-center sm:text-left"
          >
            <div class="p-5">
              <div
                class="flex justify-between text-theme-1 dark:text-theme-10 font-semibold text-3xl"
              >
                <div>
                  INVOICE
                  <small class="uppercase text-theme-10">{{
                    invToView.type
                  }}</small>
                </div>

                <paystack
                  v-if="can('invoice_pay') && !invToView.paid"
                  :amount="invToView.cost + invToView.insurance"
                  :email="invToView.user.email"
                  :paystackkey="paystack.publicKey"
                  :reference="reference()"
                  :callback="callbackPaystack"
                  :close="closePaystack"
                  :embed="false"
                >
                  <button
                    class="button rounded-full text-white bg-theme-1 text-sm px-5 inline-flex items-center"
                  >
                    <SmileIcon class="mr-2"></SmileIcon>
                    PAY NOW
                  </button>
                </paystack>
              </div>
              <div class="mt-2">
                Shipping status:
                <span class="uppercase text-theme-10">{{
                  invToView.status +
                    " at " +
                    invToView[invToView.status + "_at"]
                }}</span>
              </div>
              <div class="mt-1">
                Payment status:
                <span class="uppercase text-theme-10">{{
                  invToView.paid ? "PAID" : "UNPAID"
                }}</span>
              </div>
              <div class="mt-4">
                Receipt
                <span class="font-medium">#{{ pad(invToView.id, 5) }}</span>
              </div>
              <div class="mt-1">{{ invToView.created_at }}</div>
            </div>
            <div class="flex flex-col lg:flex-row px-5">
              <div class="">
                <div class="text-base text-gray-600">Client Details</div>
                <div
                  class="text-lg font-medium text-theme-1 dark:text-theme-10 mt-2"
                >
                  {{ invToView.user.first_name }} {{ invToView.user.last_name }}
                </div>
                <div class="mt-1">{{ invToView.user.email }}</div>
                <div v-if="invToView.address" class="mt-1">
                  {{
                    invToView.address.street +
                      " " +
                      invToView.address.street_name +
                      " " +
                      invToView.address.city
                  }}
                </div>
                <div
                  v-if="invToView.address && invToView.address.emails"
                  class="mt-1"
                >
                  Alternative emails:
                  <span
                    v-for="(email, index) in invToView.address.emails"
                    :key="'em-' + index"
                    class="mr-3"
                    >{{ email }}</span
                  >
                </div>
                <div
                  v-if="invToView.address && invToView.address.phones"
                  class="mt-1"
                >
                  Alternative phones:
                  <span
                    v-for="(phone, index) in invToView.address.phones"
                    :key="'ph-' + index"
                    class="mr-3"
                    >{{ phone }}</span
                  >
                </div>
              </div>
              <div class="lg:text-right mt-10 lg:mt-0 lg:ml-auto">
                <div class="text-base text-gray-600">Payment to</div>
                <div
                  class="text-lg font-medium text-theme-1 dark:text-theme-10 mt-2"
                >
                  AfroShipper
                </div>
                <div class="mt-1">paylike@afroshipper.com</div>
              </div>
            </div>
            <div class="flex flex-col lg:flex-row p-5">
              <div>
                <div class="text-base text-gray-600">Other details</div>
                <div class="mt-2">
                  Box:
                  <span v-if="invToView.width && invToView.height">{{
                    invToView.width + " x " + invToView.height + " m"
                  }}</span
                  >&nbsp;/&nbsp;
                  <span>{{
                    invToView.weight + " " + invToView.weight_unit
                  }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="p-5">
            <div class="overflow-x-auto">
              <table class="table">
                <thead>
                  <tr>
                    <th
                      class="border-b-2 dark:border-dark-5 whitespace-no-wrap"
                    >
                      DESCRIPTION
                    </th>
                    <th
                      class="border-b-2 dark:border-dark-5 text-right whitespace-no-wrap"
                    >
                      PRICE
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-left border-b dark:border-dark-5 w-32">
                      Cost
                    </td>
                    <td
                      class="text-right border-b dark:border-dark-5 w-32 font-medium"
                    >
                      {{ invToView.cost }} ₦
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left border-b dark:border-dark-5 w-32">
                      Insurance
                    </td>
                    <td
                      class="text-right border-b dark:border-dark-5 w-32 font-medium"
                    >
                      {{ invToView.insurance }} ₦
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="p-5 flex flex-col-reverse sm:flex-row">
            <div class="text-center sm:text-left mt-10 sm:mt-0"></div>
            <div class="text-center sm:text-right sm:ml-auto">
              <div class="text-base text-gray-600">Total Amount</div>
              <div
                class="text-xl text-theme-1 dark:text-theme-10 font-medium mt-2"
              >
                {{ invToView.cost + invToView.insurance }} ₦
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import feather from "feather-icons";
import Tabulator from "tabulator-tables";
import Toastify from "toastify-js";
import { validationMixin } from "vuelidate";
import { required, minLength, email } from "vuelidate/lib/validators";
import Multiselect from "vue-multiselect";
import { debounce } from "vue-debounce";
import paystack from "vue-paystack";

export default {
  components: {
    Multiselect,
    paystack
  },
  mixins: [validationMixin],
  validations: {
    invoice: {
      user: {
        required
      },
      type: {
        required
      },
      status: {
        required
      },
      weight: {
        required
      },
      cost: {
        required
      },
      insurance: {
        required
      }
    }
  },
  data() {
    return {
      isNew: true,
      filter: {
        user: null,
        type: "all",
        status: "all",
        paid: "all",
        page: 1,
        limit: 8
      },
      invoice: {
        id: 0,
        user: null,
        type: "shipping",
        status: "created",
        weight: "",
        weight_unit: "kg",
        width: "",
        height: "",
        total: "",
        cost: "",
        insurance: "",
        notes: [""]
      },
      total: 0,
      invoices: [],
      isLoadingUsers: false,
      isLoadingInvs: false,
      users: [],
      idToDel: 0,
      invToView: null,
      paystack: {
        publicKey: "pk_test_8dd17920f52cba53acbe781fac15991f20ef64bc"
      }
    };
  },
  computed: {
    canEdit() {
      return this.isNew || this.isOwner(this.invoice);
    }
  },
  mounted() {
    this.loadInvoices();
  },
  methods: {
    reference() {
      let text = "";
      let possible =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

      for (let i = 0; i < 10; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

      return text;
    },
    isOwner(invoice) {
      return (
        this.can("invoice_create_shipping") ||
        (invoice.created_by &&
          this.$store.state.main.user.id == invoice.created_by)
      );
    },
    can(permission) {
      return (
        this.$store.state.main.permissions.findIndex(p => p == permission) >= 0
      );
    },
    searchUsers: debounce(function(query) {
      if (!query) return;
      this.isLoadingUsers = true;
      this.$store.dispatch("main/searchUsers", query).then(res => {
        this.isLoadingUsers = false;
        if (res.success && res.users) {
          this.users = res.users;
        }
      });
    }, 300),
    initFilter() {
      this.filter = {
        user: null,
        type: "all",
        status: "all",
        paid: "all",
        limit: 8
      };
      this.filter.page = 1;
      this.invoices = [];
      this.loadInvoices();
    },
    loadMore() {
      this.filter.page += 1;
      this.loadInvoices();
    },
    applyFilter() {
      this.filter.page = 1;
      this.invoices = [];
      this.loadInvoices();
    },
    loadInvoices() {
      let filter = Object.assign({}, this.filter);
      if (this.filter.user && this.filter.user.id > 0) {
        filter.user = this.filter.user.id;
      }
      this.isLoadingInvs = true;
      this.$store.dispatch("main/invoices", filter).then(res => {
        let invoices = res.invoices.data;
        this.total = res.invoices.total;
        this.isLoadingInvs = false;
        if (invoices.length > 0)
          invoices = invoices.map(invoice => {
            invoice.notes = JSON.parse(invoice.notes);
            return invoice;
          });
        this.invoices = this.invoices.concat(invoices);
      });
    },
    initInvoice() {
      this.invoice = {
        id: 0,
        user: null,
        type: "shipping",
        status: "created",
        weight: "",
        weight_unit: "kg",
        width: "",
        height: "",
        total: "",
        cost: "",
        insurance: "",
        notes: [""]
      };
    },
    showModal(id) {
      cash("#" + id).modal("show");
    },
    closeModal(id) {
      cash("#" + id).modal("hide");
    },
    onClickAdd() {
      this.isNew = true;
      this.initInvoice();
      if (
        !this.can("invoice_create_shipping") &&
        this.can("invoice_create_dispatch")
      )
        this.invoice.type = "dispatch";
      this.showModal("invoice-modal");
    },
    onClickEdit(invoice) {
      this.invoice = Object.assign({}, invoice);
      this.isNew = false;
      this.showModal("invoice-modal");
    },
    onClickDelete(id) {
      this.idToDel = id;
      this.showModal("delete-modal");
    },
    onClickView(invoice) {
      let inv = Object.assign({}, invoice);
      if (inv.address && !Array.isArray(inv.address.emails)) {
        inv.address.emails = JSON.parse(inv.address.emails);
        inv.address.phones = JSON.parse(inv.address.phones);
      }
      this.invToView = inv;
      this.showModal("view-modal");
    },
    pad(num, size) {
      var s = "000000000" + num;
      return s.substr(s.length - size);
    },
    onClickSave() {
      let action = this.isNew ? "main/createInvoice" : "main/updateInvoice";
      let msg = this.isNew ? "Invoice created." : "Invoice updated.";
      this.$v.$touch();
      if (this.$v.$invalid) return;

      let invoice = Object.assign({}, this.invoice);
      invoice.notes = JSON.stringify(invoice.notes);
      invoice.user_id = invoice.user.id;
      this.$store
        .dispatch(action, invoice)
        .then(res => {
          Toastify({
            text: msg,
            backgroundColor: "#0e2c88",
            gravity: "bottom",
            position: "center",
            close: true
          }).showToast();
          this.initInvoice();
          this.closeModal("invoice-modal");
          this.applyFilter();
        })
        .catch(err => {
          let msg = err.response.data.message || err.response.data.errors[0];
          Toastify({
            text: msg,
            backgroundColor: "#D32929",
            gravity: "bottom",
            position: "center",
            close: true
          }).showToast();
        });
      this.isNew = true;
    },
    onConfirmDelete() {
      if (this.idToDel <= 0) return;
      this.$store
        .dispatch("main/deleteInvoice", this.idToDel)
        .then(res => {
          Toastify({
            text: "Invoice removed.",
            backgroundColor: "#0e2c88",
            gravity: "bottom",
            position: "center",
            close: true
          }).showToast();
          this.applyFilter();
        })
        .catch(err => {
          let msg = err.response.data.message || err.response.data.errors[0];
          Toastify({
            text: msg,
            backgroundColor: "#D32929",
            gravity: "bottom",
            position: "center",
            close: true
          }).showToast();
        });
      this.idToDel = 0;
      this.closeModal("delete-modal");
    },
    toggleWeightUnit() {
      this.invoice.weight_unit = this.invoice.weight_unit == "kg" ? "lb" : "kg";
    },
    addNote() {
      this.invoice.notes.push("");
    },
    removeNote(id) {
      this.invoice.notes.splice(id, 1);
    },
    callbackPaystack: function(response) {
      if (response.status == "success")
        this.$store
          .dispatch("main/pay", {
            response: response,
            id: this.invToView.id
          })
          .then(res => {
            this.closeModal("view-modal");
            this.applyFilter();
          });
    },
    closePaystack: function() {
      // console.log("Payment closed");
    }
  }
};
</script>

<style scoped>
#invoice-modal .input ~ .absolute {
  font-size: 15px;
  font-weight: 700;
}
</style>
