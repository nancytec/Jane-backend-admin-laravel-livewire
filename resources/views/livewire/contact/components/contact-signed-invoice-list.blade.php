<div>
    <div class="card-body border-bottom">
        <h4 wire:loading.remove wire:target="contact" class="card-title">
            {{count($invoices)}} signed
            @if(count($invoices) > 1)
                invoices
            @else
                invoice
            @endif
        </h4>
        <h4 wire:loading wire:target="contact" class="card-title">Searching... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></h4>


        <select wire:model="contact" class="invoiceto form-select {{$errors->has('contact')? 'is-invalid' : '' }}">
            <option value="">-- Select company--</option>
            @if($contactRecords)
                @foreach($contactRecords as $contact)
                    <option value="{{$contact->id}}">{{$contact->company->name }}</option>
                @endforeach
            @endif
        </select>
        <div class="row">
            <div class="col-md-4 user_role"></div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
        </div>
    </div>


    <table class="invoice-list-table table">
        <thead>
        <tr>

            <th>#</th>
            <th>Contact</th>
            <th>Total({{$settings->app_currency}})</th>
            <th class="text-truncate">Issued Date</th>
            <th class="text-truncate">Due Date</th>
            <th>Assigned to</th>
            <th>Invoice Status</th>
            <th class="cell-fit">Actions</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        @if($invoices)
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{$loop->index + 1}}</td>
                    <td>
                        @if($invoice->ContactInfo)
                            {{$invoice->contactInfo->firstname. '  '.$invoice->contactInfo->lastname}}
                        @else
                            <span class="text-danger">Contact deleted</span>
                        @endif
                    </td>
                    <td>{{$settings->app_currency_symbol}}{{$invoice->products_total_price + $invoice->services_total_price}}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->date_issued)->translatedFormat(' j F Y')}}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->due_date)->translatedFormat(' j F Y')}}</td>


                    @if($invoice->worker)
                        <td>{{$invoice->worker->firstname. '  ' .$invoice->worker->lastname }}</td>
                    @else
                        <span class="text-danger">Staff not available</span>
                    @endif


                    @if($invoice->signed)
                        <td>Signed</td>
                    @else
                        <td>Unsigned</td>
                    @endif
                    <td><a href="{{route('contact.invoices-preview', $invoice->id)}}">Preview</a>

                </tr>
            @endforeach
        @endif

        </tbody>
    </table>

</div>
