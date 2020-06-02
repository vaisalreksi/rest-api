<div style="display: block;width: 100%;font-family: sans-serif;font-size: 13px;color: #868686;">
    <div style="background: #6c6c6c;padding: 30px;text-align: center;margin: 0;">
    </div>
    <div style="display: inline-block;width: 100%;padding: 40px;box-sizing: border-box;background: #f4f4f4;">
            <h4 style="color: #6c6c6c;margin: 5px 0;"><i>Hallo {{ $name }}</i></h4>
            <span>Nomor SPK {{ $ref }} akan expired dalam 10 hari kedepan segera lakukan penagihan barang ke Vendor</span>
            <div style="color: blue;text-align: center;padding: 26px;background: #fff;margin: 25px 0;box-shadow: 0 0 4px rgba(0,0,0,.2);">
                <h2>Expired Date</h2>
                <span><font size="100">{{ $date }}</font></span>
            </div>
            <span>Terima kasih</span>
    </div>
</div>
