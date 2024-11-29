<adf>
    <prospect>
        <requestdate>{{ $vehicle['last_seen_at_date'] ?? date('Y-m-d\TH:i:s') }}</requestdate>
        <vehicle interest="trade-in">
            <year>{{ $vehicle['build']['year'] ?? 'null' }}</year>
            <make>{{ $vehicle['build']['make'] ?? 'null' }}</make>
            <model>{{ $vehicle['build']['model'] ?? 'null' }}</model>
            <trim>{{ $vehicle['build']['trim'] ?? 'null' }}</trim>
            <vin>{{ $vehicle['vin'] ?? 'null' }}</vin>
            <odometer>{{ $vehicle['miles'] ?? 'null' }}</odometer>
            <comments>{{ $vehicle['seller_comments'] ?? 'null' }}</comments>
        </vehicle>
        <vehicle interest="buy" status="used">
            <year>{{ $vehicle['build']['year'] ?? 'null' }}</year>
            <make>{{ $vehicle['build']['make'] ?? 'null' }}</make>
            <model>{{ $vehicle['build']['model'] ?? 'null' }}</model>
            <trim>{{ $vehicle['build']['trim'] ?? 'null' }}</trim>
            <vin>{{ $vehicle['vin'] ?? 'null' }}</vin>
            <transmission>{{ $vehicle['build']['transmission'] ?? 'null' }}</transmission>
            <stock>{{ $vehicle['id'] ?? 'null' }}</stock>
        </vehicle>
        <customer>
            <contact>
                <name part="first">{{ $user['first_name'] ?? 'null' }}</name>
                <name part="middle">{{ $user['middle_name'] ?? '' }}</name>
                <name part="last">{{ $user['last_name'] ?? 'null' }}</name>
                <email>{{ $user['email'] ?? 'null' }}</email>
                <phone type="voice">{{ $user['phone_number'] ?? 'null' }}</phone>
                <phone type="cellphone">{{ $user['cellphone'] ?? 'null' }}</phone>
                <address type="home">
                    <street line="1">{{ $user['address'] ?? 'null' }}</street>
                    <apartment>{{ $user['apartment'] ?? '' }}</apartment>
                    <city>{{ $user['city'] ?? 'null' }}</city>
                    <regioncode>{{ $user['state'] ?? 'null' }}</regioncode>
                    <postalcode>{{ $user['zip_code'] ?? 'null' }}</postalcode>
                    <country>USA</country>
                </address>
            </contact>
            <timeframe>
                <description/>
            </timeframe>
            <comments>{{ $vehicle['buyer_comments'] ?? 'null' }}</comments>
        </customer>
        <vendor>
            <vendorname>Carnext Auots</vendorname>
            <contact primary="1">
                <name part="full">Carnext Auots</name>
            </contact>
        </vendor>
        <provider>
            <name part="full">Carnext Auots</name>
            <service>Support</service>
        </provider>
    </prospect>
</adf>
