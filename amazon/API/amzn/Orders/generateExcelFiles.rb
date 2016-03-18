#!/usr/local/rvm/rubies/default/bin/ruby
#require 'rubygems'
require 'open-uri'
require 'csv'
require 'pp'
require 'axlsx'
url = '/home/seanno/public_html/admin/amazon-unshipped-orders.csv'
#url = 'http://www.lifehousemedical.com/admin/amazon-unshipped-orders.csv'
datestamp = Time.now.strftime('%Y%m%d')
original_csv = []
basedir='/home/seanno/public_html/admin'

amz_array = []
ovation_array = []
patterson_array = []
isg_array = []
medline_array = []
indemed_array = []
nbs_array = []

all_orderids_array = []
ovation_orderids_array = []
patterson_orderids_array = []
isg_orderids_array = []
medline_orderids_array = []
indemed_orderids_array = []

#puts "Fetching #{url}..."
#puts ''

#puts file
#CSV.foreach(url, :headers => :first_row).each do |line|
#CSV.foreach(url) do |line|

CSV.new(open(url), :headers => :first_row).each do |line|
if line['ship-postal-code'] && line['ship-postal-code'] != ''
	line['ship-postal-code'].to_s! if line['ship-postal-code'].class != String
end
	original_csv << line.to_hash
	amz_array << line.to_hash
end

Axlsx::Package.new do |p|
	p.workbook.add_worksheet(:name => 'AMZ') do |sheet|
		text_format = p.workbook.styles.add_style :format_code => '@'
		yellow_format = p.workbook.styles.add_style :format_code => '@', :bg_color => 'FFFF00'
		blue_format = p.workbook.styles.add_style :format_code => '@', :bg_color => '00FFFF'
		sheet.add_row ['Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 
			'Quantity', 'ShipService', 'PurchaseOrder']
		original_csv.each do |row|
			formatting_array = [text_format] * 11
			if (row['quantity-to-ship'].to_i > 1)
				formatting_array[8] = yellow_format
			end
			if (row['ship-service-level'] != "Standard")
				formatting_array[9] = blue_format
			end 
			sheet.add_row [row['recipient-name'], row['ship-address-1'], row['ship-address-2'], 
				row['ship-city'], row['ship-state'], "=\"#{row['ship-postal-code']}\"", row['sku'], 
				row['product-name'], row['quantity-to-ship'], row['ship-service-level'], "=\"#{row['order-id'].gsub("'","")}\""], :style => formatting_array
		end
		sheet.column_widths nil, nil, nil, nil, nil, nil, nil, nil, nil, nil, nil
	end
	p.serialize(File.expand_path(basedir + "/amazon-templates/AMZ-#{datestamp}.xlsx"))
end

original_csv.each do |row|
	all_orderids_array << row['order-id'];
	if (row['sku'].include?('NMI')) && (!row['product-name'].include?('Ovation')) && (!row['product-name'].include?('Adjustable Plantar Fasciitis'))
		nbs_array << row
		next
	end
	if (row['product-name'].include?('Ovation')) || (row['product-name'].include?('Adjustable Plantar Fasciitis'))
		ovation_array << row
		ovation_orderids_array << row['order-id'];
		next
	end
	if (row['sku'].include?('CTS'))
		patterson_array << row
		patterson_orderids_array << row['order-id'];
		next
	end
	if (row['sku'].include?('MD44')) || (row['sku'].include?('MDLN')) || (row['sku'].include?('MD88'))
		medline_array << row
		medline_orderids_array << row['order-id'];
		next
	end
	if ((row['sku'].count('-') == 0) && (!row['product-name'].include?('Ovation'))) || (((row['sku'].include?('IND')) || (row['sku'].include?('UHS')) && (row['sku'].count('-') == 1)))
		indemed_array << row
		indemed_orderids_array << row['order-id'];
		next
	end
	if (row['sku'].count('-') > 1)
		isg_array << row
		isg_orderids_array << row['order-id'];
		next
	end
	nbs_array << row
end

Axlsx::Package.new do |p|
	p.workbook.add_worksheet(:name => 'NBS') do |sheet|
		text_format = p.workbook.styles.add_style :format_code => '@'
		yellow_format = p.workbook.styles.add_style :format_code => '@', :bg_color => 'FFFF00'
		blue_format = p.workbook.styles.add_style :format_code => '@', :bg_color => '00FFFF'
		sheet.add_row ['Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 
			'Quantity', 'ShipService', 'PurchaseOrder']
		ovation_array.each do |row|
			formatting_array = [text_format] * 11
			if (row['quantity-to-ship'].to_i > 1)
				formatting_array[8] = yellow_format
			end
			if (row['ship-service-level'] != "Standard")
				formatting_array[9] = blue_format
			end 
			sheet.add_row [row['recipient-name'], row['ship-address-1'], row['ship-address-2'], 
				row['ship-city'], row['ship-state'], "=\"#{row['ship-postal-code']}\"", row['sku'], 
				row['product-name'], row['quantity-to-ship'], row['ship-service-level'], "=\"#{row['order-id'].gsub("'","")}\""], :style => formatting_array
		end
		sheet.column_widths nil, nil, nil, nil, nil, nil, nil, nil, nil, nil, nil
	end
	p.serialize(File.expand_path(basedir + "/amazon-templates/Ovation-#{datestamp}.xlsx"))
end

Axlsx::Package.new do |p|
	p.workbook.add_worksheet(:name => 'NBS') do |sheet|
		text_format = p.workbook.styles.add_style :format_code => '@'
		yellow_format = p.workbook.styles.add_style :format_code => '@', :bg_color => 'FFFF00'
		blue_format = p.workbook.styles.add_style :format_code => '@', :bg_color => '00FFFF'
		sheet.add_row ['Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 
			'Quantity', 'ShipService', 'PurchaseOrder']
		nbs_array.each do |row|
			formatting_array = [text_format] * 11
			if (row['quantity-to-ship'].to_i > 1)
				formatting_array[8] = yellow_format
			end
			if (row['ship-service-level'] != "Standard")
				formatting_array[9] = blue_format
			end 
			sheet.add_row [row['recipient-name'], row['ship-address-1'], row['ship-address-2'], 
				row['ship-city'], row['ship-state'], "=\"#{row['ship-postal-code']}\"", row['sku'], 
				row['product-name'], row['quantity-to-ship'], row['ship-service-level'], "=\"#{row['order-id'].gsub("'","")}\""], :style => formatting_array
		end
		sheet.column_widths nil, nil, nil, nil, nil, nil, nil, nil, nil, nil, nil
	end
	p.serialize(File.expand_path(basedir + "/amazon-templates/NBS-#{datestamp}.xlsx"))
end

Axlsx::Package.new do |p|
	p.workbook.add_worksheet(:name => 'Patterson') do |sheet|
		text_format = p.workbook.styles.add_style :format_code => '@'
		yellow_format = p.workbook.styles.add_style :format_code => '@', :bg_color => 'FFFF00'
		blue_format = p.workbook.styles.add_style :format_code => '@', :bg_color => '00FFFF'
		sheet.add_row ['Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 
			'Quantity', 'ShipService', 'PurchaseOrder']
		patterson_array.each do |row|
			formatting_array = [text_format] * 11
			if (row['quantity-to-ship'].to_i > 1)
				formatting_array[8] = yellow_format
			end
			if (row['ship-service-level'] != "Standard")
				formatting_array[9] = blue_format
			end 
			sheet.add_row [row['recipient-name'], row['ship-address-1'], row['ship-address-2'], 
				row['ship-city'], row['ship-state'], "=\"#{row['ship-postal-code']}\"", row['sku'].gsub("CTS101-",""), 
				row['product-name'], row['quantity-to-ship'], row['ship-service-level'], "=\"#{row['order-id'].gsub("'","")}\""], :style => formatting_array
		end
		sheet.column_widths nil, nil, nil, nil, nil, nil, nil, nil, nil, nil, nil
	end
	p.serialize(File.expand_path(basedir + "/amazon-templates/Patterson-#{datestamp}.xlsx"))
end

Axlsx::Package.new do |p|
	p.workbook.add_worksheet(:name => 'Medline') do |sheet|
		text_format = p.workbook.styles.add_style :format_code => '@'
		yellow_format = p.workbook.styles.add_style :format_code => '@', :bg_color => 'FFFF00'
		blue_format = p.workbook.styles.add_style :format_code => '@', :bg_color => '00FFFF'
		sheet.add_row ['Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 
			'Quantity', 'ShipService', 'PurchaseOrder']
		medline_array.each do |row|
			formatting_array = [text_format] * 11
			if (row['quantity-to-ship'].to_i > 1)
				formatting_array[8] = yellow_format
			end
			if (row['ship-service-level'] != "Standard")
				formatting_array[9] = blue_format
			end 
			sheet.add_row [row['recipient-name'], row['ship-address-1'], row['ship-address-2'], 
				row['ship-city'], row['ship-state'], "=\"#{row['ship-postal-code']}\"", row['sku'].gsub(/MDLN1|MD44|MD88|MDLN-/,""), 
				row['product-name'], row['quantity-to-ship'], row['ship-service-level'], "=\"#{row['order-id'].gsub("'","")}\""], :style => formatting_array
		end
		sheet.column_widths nil, nil, nil, nil, nil, nil, nil, nil, nil, nil, nil
	end
	p.serialize(File.expand_path(basedir + "/amazon-templates/Medline-#{datestamp}.xlsx"))
end

Axlsx::Package.new do |p|
	p.workbook.add_worksheet(:name => 'Indemed') do |sheet|
		text_format = p.workbook.styles.add_style :format_code => '@'
		yellow_format = p.workbook.styles.add_style :format_code => '@', :bg_color => 'FFFF00'
		blue_format = p.workbook.styles.add_style :format_code => '@', :bg_color => '00FFFF'
		sheet.add_row ['Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 
			'Quantity', 'ShipService', 'PurchaseOrder']
		indemed_array.each do |row|
			formatting_array = [text_format] * 11
			if (row['quantity-to-ship'].to_i > 1)
				formatting_array[8] = yellow_format
			end
			if (row['ship-service-level'] != "Standard")
				formatting_array[9] = blue_format
			end 
			sheet.add_row [row['recipient-name'], row['ship-address-1'], row['ship-address-2'], 
				row['ship-city'], row['ship-state'], "=\"#{row['ship-postal-code']}\"", row['sku'].gsub(/IND-|INDP-|UHS-/,""), 
				row['product-name'], row['quantity-to-ship'], row['ship-service-level'], "=\"#{row['order-id'].gsub("'","")}\""], :style => formatting_array
		end
		sheet.column_widths nil, nil, nil, nil, nil, nil, nil, nil, nil, nil, nil
	end
	p.serialize(File.expand_path(basedir + "/amazon-templates/Indemed-#{datestamp}.xlsx"))
end

Axlsx::Package.new do |p|
	p.workbook.add_worksheet(:name => 'ISG') do |sheet|
		text_format = p.workbook.styles.add_style :format_code => '@'
		yellow_format = p.workbook.styles.add_style :format_code => '@', :bg_color => 'FFFF00'
		blue_format = p.workbook.styles.add_style :format_code => '@', :bg_color => '00FFFF'
		sheet.add_row ['Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 
			'Quantity', 'ShipService', 'PurchaseOrder']
		isg_array.each do |row|
			formatting_array = [text_format] * 11
			if (row['quantity-to-ship'].to_i > 1)
				formatting_array[8] = yellow_format
			end
			if (row['ship-service-level'] != "Standard")
				formatting_array[9] = blue_format
			end 
			sheet.add_row [row['recipient-name'], row['ship-address-1'], row['ship-address-2'], 
				row['ship-city'], row['ship-state'], "=\"#{row['ship-postal-code']}\"", row['sku'].gsub(/UHS-|ISG-/,""), 
				row['product-name'], row['quantity-to-ship'], row['ship-service-level'], "=\"#{row['order-id'].gsub("'","")}\""], :style => formatting_array
		end
		sheet.column_widths nil, nil, nil, nil, nil, nil, nil, nil, nil, nil, nil
	end
	p.serialize(File.expand_path(basedir + "/amazon-templates/ISG-#{datestamp}.xlsx"))
end

order_ids = all_orderids_array.join(",").gsub("'", '')
f = File.new(basedir + '/amazon-templates/all.csv', 'w')
f.write("#{order_ids}\n")
f.close();

order_ids = ovation_orderids_array.join(",").gsub("'", '')
f = File.new(basedir + '/amazon-templates/ovation.csv', 'w')
f.write("#{order_ids}\n")
f.close();

order_ids = patterson_orderids_array.join(",").gsub("'", '')
f = File.new(basedir + '/amazon-templates/patterson.csv', 'w')
f.write("#{order_ids}\n")
f.close();

order_ids = isg_orderids_array.join(",").gsub("'", '')
f = File.new(basedir + '/amazon-templates/isg.csv', 'w')
f.write("#{order_ids}\n")
f.close();

order_ids = medline_orderids_array.join(",").gsub("'", '')
f = File.new(basedir + '/amazon-templates/medline.csv', 'w')
f.write("#{order_ids}\n")
f.close();

order_ids = indemed_orderids_array.join(",").gsub("'", '')
f = File.new(basedir + '/amazon-templates/indemed.csv', 'w')
f.write("#{order_ids}\n")
f.close();

#puts "Original CSV:\t\t#{original_csv.count} record(s)."
#puts "AMZ-#{datestamp}:\t\t#{amz_array.count} record(s)"
#puts "Patterson-#{datestamp}:\t#{patterson_array.count} record(s)"
#puts "Indemed-#{datestamp}:\t#{indemed_array.count} record(s)"
#puts "ISG-#{datestamp}:\t\t#{isg_array.count} record(s)"
#puts "NBS-#{datestamp}:\t\t#{nbs_array.count} record(s)"
#puts "Medline-#{datestamp}:\t#{medline_array.count} record(s)"
#
#puts "Total:\t\t\t#{patterson_array.count + indemed_array.count + isg_array.count + nbs_array.count + medline_array.count} record(s)"
#puts ''
#puts 'Excel files successfully generated. You may now close this window.'

exit
